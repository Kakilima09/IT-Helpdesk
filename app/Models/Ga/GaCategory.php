<?php

namespace App\Models\Ga;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GaCategory extends Model
{
    use HasFactory;

    protected $table = 'ga_categories';

    const CODE_ATK = 'ATK';
    const CODE_RTK = 'RTK';

    protected $fillable = ['code', 'name', 'status'];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function items()
    {
        return $this->hasMany(GaRequestItem::class, 'category_id');
    }
}