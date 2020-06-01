<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class payments extends Model
{
  protected $fillable = [
    'payment_id', 'address_id', 'price','price_old','date','status'
];
}
