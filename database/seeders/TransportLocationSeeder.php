<?php

namespace Database\Seeders;

use App\Models\Papd\TransportLocation;
use Illuminate\Database\Seeder;

class TransportLocationSeeder extends Seeder
{
    public function run()
    {
        $locations = [
            // ===== Bandara Indonesia =====
            ['CGK', 'Soekarno-Hatta International Airport', 'Jakarta', 'airport', 'Indonesia'],
            ['HLP', 'Halim Perdanakusuma Airport', 'Jakarta', 'airport', 'Indonesia'],
            ['DPS', 'I Gusti Ngurah Rai International Airport', 'Denpasar', 'airport', 'Indonesia'],
            ['SUB', 'Juanda International Airport', 'Surabaya', 'airport', 'Indonesia'],
            ['KNO', 'Kualanamu International Airport', 'Medan', 'airport', 'Indonesia'],
            ['UPG', 'Sultan Hasanuddin International Airport', 'Makassar', 'airport', 'Indonesia'],
            ['YIA', 'Yogyakarta International Airport', 'Yogyakarta', 'airport', 'Indonesia'],
            ['JOG', 'Adisutjipto International Airport', 'Yogyakarta', 'airport', 'Indonesia'],
            ['SOC', 'Adi Soemarmo International Airport', 'Solo', 'airport', 'Indonesia'],
            ['SRG', 'Jenderal Ahmad Yani International Airport', 'Semarang', 'airport', 'Indonesia'],
            ['BPN', 'Sultan Aji Muhammad Sulaiman Sepinggan Airport', 'Balikpapan', 'airport', 'Indonesia'],
            ['PLM', 'Sultan Mahmud Badaruddin II Airport', 'Palembang', 'airport', 'Indonesia'],
            ['PKU', 'Sultan Syarif Kasim II Airport', 'Pekanbaru', 'airport', 'Indonesia'],
            ['BTH', 'Hang Nadim International Airport', 'Batam', 'airport', 'Indonesia'],
            ['PDG', 'Minangkabau International Airport', 'Padang', 'airport', 'Indonesia'],
            ['BDO', 'Husein Sastranegara International Airport', 'Bandung', 'airport', 'Indonesia'],
            ['KJT', 'Kertajati International Airport', 'Majalengka', 'airport', 'Indonesia'],
            ['LOP', 'Lombok International Airport', 'Lombok', 'airport', 'Indonesia'],
            ['DJJ', 'Sentani Airport', 'Jayapura', 'airport', 'Indonesia'],
            ['MDC', 'Sam Ratulangi International Airport', 'Manado', 'airport', 'Indonesia'],
            ['AMQ', 'Pattimura Airport', 'Ambon', 'airport', 'Indonesia'],
            ['TKG', 'Radin Inten II Airport', 'Bandar Lampung', 'airport', 'Indonesia'],
            ['TJQ', 'H.A.S. Hanandjoeddin Airport', 'Tanjung Pandan', 'airport', 'Indonesia'],
            ['TNJ', 'Raja Haji Fisabilillah Airport', 'Tanjung Pinang', 'airport', 'Indonesia'],
            ['PGK', 'Depati Amir Airport', 'Pangkal Pinang', 'airport', 'Indonesia'],
            ['BTJ', 'Sultan Iskandar Muda International Airport', 'Banda Aceh', 'airport', 'Indonesia'],
            ['PNK', 'Supadio International Airport', 'Pontianak', 'airport', 'Indonesia'],
            ['BDJ', 'Syamsudin Noor International Airport', 'Banjarmasin', 'airport', 'Indonesia'],
            ['PKY', 'Tjilik Riwut Airport', 'Palangkaraya', 'airport', 'Indonesia'],
            ['KOE', 'El Tari Airport', 'Kupang', 'airport', 'Indonesia'],

            // ===== Bandara Internasional =====
            ['SIN', 'Changi International Airport', 'Singapore', 'airport', 'Singapore'],
            ['KUL', 'Kuala Lumpur International Airport', 'Kuala Lumpur', 'airport', 'Malaysia'],
            ['BKK', 'Suvarnabhumi Airport', 'Bangkok', 'airport', 'Thailand'],
            ['HKG', 'Hong Kong International Airport', 'Hong Kong', 'airport', 'Hong Kong'],
            ['TPE', 'Taoyuan International Airport', 'Taipei', 'airport', 'Taiwan'],
            ['ICN', 'Incheon International Airport', 'Seoul', 'airport', 'South Korea'],
            ['NRT', 'Narita International Airport', 'Tokyo', 'airport', 'Japan'],
            ['HND', 'Haneda Airport', 'Tokyo', 'airport', 'Japan'],
            ['PVG', 'Pudong International Airport', 'Shanghai', 'airport', 'China'],
            ['CAN', 'Baiyun International Airport', 'Guangzhou', 'airport', 'China'],
            ['SYD', 'Kingsford Smith Airport', 'Sydney', 'airport', 'Australia'],
            ['MEL', 'Melbourne Airport', 'Melbourne', 'airport', 'Australia'],
            ['JED', 'King Abdulaziz International Airport', 'Jeddah', 'airport', 'Saudi Arabia'],
            ['MED', 'Prince Mohammad bin Abdulaziz Airport', 'Madinah', 'airport', 'Saudi Arabia'],
            ['DOH', 'Hamad International Airport', 'Doha', 'airport', 'Qatar'],
            ['DXB', 'Dubai International Airport', 'Dubai', 'airport', 'United Arab Emirates'],
            ['AUH', 'Zayed International Airport', 'Abu Dhabi', 'airport', 'United Arab Emirates'],
            ['AMS', 'Schiphol Airport', 'Amsterdam', 'airport', 'Netherlands'],
            ['LHR', 'Heathrow Airport', 'London', 'airport', 'United Kingdom'],
            ['CDG', 'Charles de Gaulle Airport', 'Paris', 'airport', 'France'],
            ['FRA', 'Frankfurt Airport', 'Frankfurt', 'airport', 'Germany'],

            // ===== Stasiun Kereta =====
            ['GMR', 'Stasiun Gambir', 'Jakarta', 'train', 'Indonesia'],
            ['PSE', 'Stasiun Pasar Senen', 'Jakarta', 'train', 'Indonesia'],
            ['BD', 'Stasiun Bandung', 'Bandung', 'train', 'Indonesia'],
            ['SBU', 'Stasiun Surabaya Gubeng', 'Surabaya', 'train', 'Indonesia'],
            ['SBI', 'Stasiun Surabaya Pasar Turi', 'Surabaya', 'train', 'Indonesia'],
            ['YK', 'Stasiun Yogyakarta Tugu', 'Yogyakarta', 'train', 'Indonesia'],
            ['SLO', 'Stasiun Solo Balapan', 'Solo', 'train', 'Indonesia'],
            ['SMC', 'Stasiun Semarang Tawang', 'Semarang', 'train', 'Indonesia'],
            ['ML', 'Stasiun Malang', 'Malang', 'train', 'Indonesia'],
            ['MDN', 'Stasiun Medan', 'Medan', 'train', 'Indonesia'],

            // ===== Pelabuhan =====
            ['TPR', 'Pelabuhan Tanjung Priok', 'Jakarta', 'port', 'Indonesia'],
            ['TPS', 'Pelabuhan Tanjung Perak', 'Surabaya', 'port', 'Indonesia'],
            ['BTM', 'Pelabuhan Batam', 'Batam', 'port', 'Indonesia'],

            // ===== Terminal Bus / Travel =====
            ['TRM-PG', 'Terminal Pulo Gebang', 'Jakarta', 'bus', 'Indonesia'],
            ['TRM-KR', 'Terminal Kampung Rambutan', 'Jakarta', 'bus', 'Indonesia'],
            ['TRM-KLD', 'Terminal Kalideres', 'Jakarta', 'bus', 'Indonesia'],
            ['TRM-LWP', 'Terminal Leuwi Panjang', 'Bandung', 'bus', 'Indonesia'],
            ['TRM-CCH', 'Terminal Cicaheum', 'Bandung', 'bus', 'Indonesia'],
            ['TRM-BSH', 'Terminal Bungurasih', 'Surabaya', 'bus', 'Indonesia'],
            ['TRM-GIW', 'Terminal Giwangan', 'Yogyakarta', 'bus', 'Indonesia'],
            ['TRM-TRT', 'Terminal Tirtonadi', 'Solo', 'bus', 'Indonesia'],
            ['TRM-ARJ', 'Terminal Arjosari', 'Malang', 'bus', 'Indonesia'],
            ['TRM-UBG', 'Terminal Ubung', 'Denpasar', 'bus', 'Indonesia'],
            ['TRM-AMP', 'Terminal Amplas', 'Medan', 'bus', 'Indonesia'],
            ['TRM-KRY', 'Terminal Karya Jaya', 'Palembang', 'bus', 'Indonesia'],
        ];

        foreach ($locations as $loc) {
            TransportLocation::updateOrCreate(
                ['code' => $loc[0]],
                [
                    'name' => $loc[1],
                    'city' => $loc[2],
                    'type' => $loc[3],
                    'country' => $loc[4],
                    'is_active' => true,
                ]
            );
        }
    }
}
