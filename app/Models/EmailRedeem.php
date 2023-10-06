<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailRedeem extends Model
{
    protected $fillable = [
        'email',
        'code_id',
    ];
}
