<?php

namespace App\Services;

use App\Models\Papd\PapdRequest;
use Carbon\Carbon;

class PapdRecommendationService
{
    /**
     * Bangun daftar link rekomendasi untuk sebuah PAPD.
     *
     * @return array<int, array{key:string,label:string,url:string,type:string,icon:string}>
     */
    public function buildLinks(PapdRequest $papd)
    {
        $providers = config('papd_recommendations.providers', []);
        $links = [];

        $hasFlight = $papd->moda_transportasi === 'pesawat';
        $hasHotel  = (bool) $papd->hotel_reservasi;
        $hasReturnFlight = $papd->moda_transportasi_pulang === 'pesawat'
            && !empty($papd->kode_asal_pulang)
            && !empty($papd->kode_tujuan_pulang);

        $placeholders = $this->buildPlaceholders($papd);

        foreach ($providers as $key => $provider) {
            if (empty($provider['enabled']) || empty($provider['url_template'])) {
                continue;
            }

            $type = $provider['type'] ?? 'flight';

            if ($type === 'flight' && !$hasFlight) {
                continue;
            }
            if ($type === 'hotel' && !$hasHotel) {
                continue;
            }

            // Lewati bila placeholder wajib tidak tersedia
            if ($type === 'flight' && (empty($placeholders['{origin}']) || empty($placeholders['{destination}']))) {
                continue;
            }
            if ($type === 'hotel' && empty($placeholders['{city}'])) {
                continue;
            }

            $url = strtr($provider['url_template'], $placeholders);

            $links[] = [
                'key'   => $key,
                'label' => $provider['label'] ?? ucfirst($key),
                'url'   => $url,
                'type'  => $type,
                'icon'  => $provider['icon'] ?? 'fe fe-link',
            ];
        }

        if ($hasReturnFlight) {
            $returnPlaceholders = $this->buildPlaceholders($papd, true);

            foreach ($providers as $key => $provider) {
                if (empty($provider['enabled']) || empty($provider['url_template'])) {
                    continue;
                }
                if (($provider['type'] ?? 'flight') !== 'flight') {
                    continue;
                }
                if (empty($returnPlaceholders['{origin}']) || empty($returnPlaceholders['{destination}'])) {
                    continue;
                }

                $links[] = [
                    'key'   => $key . '_return',
                    'label' => ($provider['label'] ?? ucfirst($key)) . ' (Pulang)',
                    'url'   => strtr($provider['url_template'], $returnPlaceholders),
                    'type'  => 'flight',
                    'icon'  => $provider['icon'] ?? 'fe fe-link',
                ];
            }
        }

        return $links;
    }

    protected function buildPlaceholders(PapdRequest $papd, $returnTrip = false)
    {
        if ($returnTrip) {
            $originCode      = !empty($papd->kode_asal_pulang) ? strtoupper($papd->kode_asal_pulang) : '';
            $destinationCode = !empty($papd->kode_tujuan_pulang) ? strtoupper($papd->kode_tujuan_pulang) : '';
            $depart = $papd->tanggal_kepulangan
                ? Carbon::parse($papd->tanggal_kepulangan)
                : Carbon::now();
            $kelas = $papd->kelas_pulang ?: $papd->kelas;
        } else {
            $originCode      = $this->resolveOriginCode($papd);
            $destinationCode = $this->resolveDestinationCode($papd);
            $depart = $papd->tanggal_keberangkatan
                ? Carbon::parse($papd->tanggal_keberangkatan)
                : Carbon::now();
            $kelas = $papd->kelas;
        }

        $return = $papd->tanggal_kepulangan
            ? Carbon::parse($papd->tanggal_kepulangan)
            : $depart->copy()->addDay();

        $checkIn  = $papd->check_in ? Carbon::parse($papd->check_in) : $depart->copy();
        $checkOut = $papd->check_out ? Carbon::parse($papd->check_out) : $return->copy();

        $adults = 1;
        if ($papd->role_perjalanan === 'team' && !empty($papd->jumlah_anggota_team)) {
            $adults = 1 + (int) $papd->jumlah_anggota_team;
        }

        $city = $papd->lokasi_hotel ?: $papd->kota_tujuan ?: '';

        return [
            '{origin}'           => $originCode,
            '{destination}'      => $destinationCode,
            '{origin_lower}'     => strtolower($originCode),
            '{destination_lower}'=> strtolower($destinationCode),
            '{date}'             => $depart->format('Y-m-d'),
            '{date_dmy}'         => $depart->format('d-m-Y'),
            '{date_ymd}'         => $depart->format('Y-m-d'),
            '{date_yymmdd}'      => $depart->format('ymd'),
            '{return_date}'      => $return->format('Y-m-d'),
            '{return_dmy}'       => $return->format('d-m-Y'),
            '{return_ymd}'       => $return->format('Y-m-d'),
            '{return_yymmdd}'    => $return->format('ymd'),
            '{adults}'           => $adults,
            '{cabin}'            => $this->mapCabin($kelas),
            '{city}'             => urlencode($city),
            '{rooms}'            => $papd->jumlah_kamar ?: 1,
            // Hotel memakai tanggal check-in/out bila ada
            '{checkin_ymd}'      => $checkIn->format('Y-m-d'),
            '{checkout_ymd}'     => $checkOut->format('Y-m-d'),
        ];
    }

    protected function resolveOriginCode(PapdRequest $papd)
    {
        if (!empty($papd->kode_asal)) {
            return strtoupper($papd->kode_asal);
        }

        $city = $papd->kota_asal;
        if (empty($city) && !empty($papd->rute)) {
            $parts = preg_split('/\s*(?:-|–|->|to)\s*/i', $papd->rute);
            $city = $parts[0] ?? null;
        }
        if (empty($city)) {
            $city = config('papd_recommendations.default_origin', 'Jakarta');
        }

        return $this->codeFromCity($city);
    }

    protected function resolveDestinationCode(PapdRequest $papd)
    {
        if (!empty($papd->kode_tujuan)) {
            return strtoupper($papd->kode_tujuan);
        }

        return $this->codeFromCity($papd->kota_tujuan);
    }

    protected function codeFromCity($city)
    {
        if (empty($city)) {
            return '';
        }

        $normalized = strtolower(trim($city));
        $map = config('papd_recommendations.city_codes', []);

        if (isset($map[$normalized])) {
            return $map[$normalized];
        }

        // Coba cocokkan sebagian (mis. "Kota Jakarta")
        foreach ($map as $name => $code) {
            if (strpos($normalized, $name) !== false) {
                return $code;
            }
        }

        return '';
    }

    protected function mapCabin($kelas)
    {
        $kelas = strtolower((string) $kelas);

        switch ($kelas) {
            case 'bisnis':
                return 'BUSINESS';
            case 'eksekutif':
                return 'BUSINESS';
            case 'vip':
                return 'FIRST';
            case 'ekonomi':
            default:
                return 'ECONOMY';
        }
    }
}
