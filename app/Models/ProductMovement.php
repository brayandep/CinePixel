<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'movement_type',
        'quantity',
        'unit_price',
        'total_price',
        'stock_after',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
