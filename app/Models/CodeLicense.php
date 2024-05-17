<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CodeLicense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code_id',
        'setting_license_id',
    ];
}
