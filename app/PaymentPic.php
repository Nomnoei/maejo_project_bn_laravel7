<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentPic extends Model
{
  protected $fillable = [
      'id','address_id','payment_id', 'picture','price','status'
    ];
}
