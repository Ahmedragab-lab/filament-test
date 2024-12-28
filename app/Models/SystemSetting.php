<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'company_logo',
        'company_name',
        'time_zone',
        'date_format',
        'language',
        'theme',
    ];
}
