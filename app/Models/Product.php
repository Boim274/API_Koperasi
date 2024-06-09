<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    public $appends = ['image_url', 'nama_baru'];

    public function Kategori():BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image !== null ? asset('storage/'.$this->image) : null;
    }
    
    public function getNamaBaruAttribute()
    {
        // name = product_name
        return strtoupper($this->product_name);
    }


}

