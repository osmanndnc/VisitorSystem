<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tc_no',
        'phone',
        'plate',
        'entry_time',
        'person_to_visit',
        'visit_reason',
        'approved_by',
    ];

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    // Ziyaretçiyi onaylayan kullanıcı ile ilişki
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}