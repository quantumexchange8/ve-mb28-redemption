<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SettingLicense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'url',
        'category',
        'valid_year',
    ];
}
