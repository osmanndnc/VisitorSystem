<?php

// app/Models/Visit.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Person; // <— ekle

class Visit extends Model
{
    use HasFactory;

    protected $table = 'visit';

    protected $casts = [
        'entry_time' => 'datetime',
    ];

    protected $fillable = [
        'visitor_id',
        'phone',
        'plate',
        'entry_time',
        'exit_time',
        'purpose',
        'purpose_note',
        'approved_by',
        'department_id',
        'person_to_visit',
    ];

    public function visitor(){ return $this->belongsTo(Visitor::class); }
    public function approver(){ return $this->belongsTo(User::class, 'approved_by','id'); }
    public function department(){ return $this->belongsTo(Department::class); }

    // **ID gelirse isme çeviren okunabilir label**
    public function getPersonToVisitLabelAttribute(): string
    {
        $v = (string)($this->person_to_visit ?? '');
        if ($v !== '' && is_numeric($v)) {
            $p = Person::find($v);
            return $p?->name ?? $v;
        }
        return $v;
    }
}
