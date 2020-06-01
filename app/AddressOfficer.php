<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AddressOfficer extends Model
{
  protected $fillable = [
      'user_id','house','status'
];
}
