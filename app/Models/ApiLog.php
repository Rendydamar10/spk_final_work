<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'action',
        'status',
        'status_code',
        'message',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
