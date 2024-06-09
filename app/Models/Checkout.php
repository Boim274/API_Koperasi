<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    use HasFactory;

    public $table = 'checkouts';

    public function cart()
    {
        return $this->hasMany(AddToCart::class);
    }

    public  $request = [];
}
