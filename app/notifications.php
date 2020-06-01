<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class notifications extends Model
{
  protected $fillable = [
        'whatever_id','user_id', 'type', 'detail','status'
  ];
}
