<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    use HasFactory;

    protected $fillable = [
        'status', 'expired_date'
    ];

    protected $casts = [
        'expired_date' => 'datetime',
    ];

    
}
