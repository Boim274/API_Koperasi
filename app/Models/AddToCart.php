<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddToCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'customer_id',
        'qty',
        'harga_satuan',
        'total_harga',
        'checkout_id', // Tambahkan checkout_id ke dalam fillable
    ];

    public $table = "add_to_carts";

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
