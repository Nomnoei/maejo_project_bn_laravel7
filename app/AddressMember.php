<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AddressMember extends Model
{
  protected $fillable = [
      'house_num', 'alley', 'moo','district','alley','user_id','status','type_pay_id','patment_date'
    ];
}
