<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferHistories extends Model
{
  protected $fillable = [
      'id_show', 'address_id', 'user_id_transfer','user_id_receive'
    ];
}
