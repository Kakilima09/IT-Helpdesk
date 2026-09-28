<?php

namespace App\Models\Ga;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GaRequestItem extends Model
{
    use HasFactory;

    protected $table = 'ga_request_items';

    protected $fillable = [
        'ga_request_id', 'item_type', 'category_id', 'name', 'qty', 'unit', 'price', 'amount',
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'float',
        'amount' => 'float',
    ];

    public function gaRequest()
    {
        return $this->belongsTo(GaRequest::class, 'ga_request_id');
    }

    public function category()
    {
        return $this->belongsTo(GaCategory::class, 'category_id');
    }
}