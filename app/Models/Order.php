<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable =[
        'user_id',
        'location_id',
        'total_price',
        'date_of_delivery',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class,'location_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class,'');
    }
}
