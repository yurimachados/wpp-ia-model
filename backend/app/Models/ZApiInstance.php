<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZApiInstance extends Model
{
    use HasFactory;

    protected $table = 'z_api_instances';

    protected $fillable = [
        'instance_id',
        'instance_token',
        'security_token',
        'phone',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];
}
