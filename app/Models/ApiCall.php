<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiCall extends Model
{
    protected $fillable = [
        'request_url',
        'response_body',
        'status_code',
        'response_time',
    ]; 
}
