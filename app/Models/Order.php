<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'client_identity',
        'client_address',
        'client_country',
    ];

    public function orderContents()
    {
        return $this->hasMany(OrderContent::class);
    }
}
