<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contact_id',
        'call_type',
        'call_date',
        'duration',
        'status',
        'notes',
        'follow_up_date',
        'started_at'
    ];

    protected $casts = [
        'call_date' => 'datetime',
        'follow_up_date' => 'datetime',
        'duration' => 'integer',
        'started_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
} 