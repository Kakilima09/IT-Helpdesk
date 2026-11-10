<?php

namespace App\Models\Papd;

use Illuminate\Database\Eloquent\Model;

class TransportLocation extends Model
{
    protected $table = 'transport_locations';

    protected $fillable = [
        'code', 'name', 'city', 'type', 'country', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('code', 'LIKE', '%' . $term . '%')
              ->orWhere('name', 'LIKE', '%' . $term . '%')
              ->orWhere('city', 'LIKE', '%' . $term . '%');
        });
    }

    public function getDisplayLabelAttribute()
    {
        return $this->city . ' (' . $this->code . ') - ' . $this->name;
    }
}
