<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable =[

        'category_id',
        'brand_id',
        'name',
        'is_trandy',
        'is_available',
        'price',
        'amount',
        'descount',
        'image'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class,'brand_id','id');
    }
}
