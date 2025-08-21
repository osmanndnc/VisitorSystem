<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;
    //entry_time'ı string değil de datetime formatında almak için.
    protected $casts = [
        'entry_time' => 'datetime', 
    ];

    protected $fillable = [
        'visitor_id',
        'phone',
        'plate',
        'entry_time',
        'exit_time',
        'person_to_visit',
        'purpose',
        'purpose_note',
        'approved_by',
    ];
    //Varsayılan olarak visits arandığı için visit olarak belirtildi
    protected $table = 'visit';

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by','id');
    }
}