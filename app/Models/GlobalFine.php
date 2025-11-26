<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalFine extends Model
{
    protected $fillable = [
        'organisation','regulator','sector','region','fine_amount',
        'currency','fine_date','law','articles_breached','violation_type',
        'summary','badges','link_to_case'
    ];
}
