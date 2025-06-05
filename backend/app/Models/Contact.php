<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'company',
        'position',
        'phone',
        'email',
        'notes',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function callLogs()
    {
        return $this->hasMany(CallLog::class);
    }
} 