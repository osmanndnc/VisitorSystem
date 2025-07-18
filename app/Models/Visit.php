<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'entry_time',
        'exit_time',
        'person_to_visit',
        'purpose',
    ];
    // Tablonun adı tekil olduğu için belirtildi
    protected $table = 'visit';

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

        // Ziyaretçiyi onaylayan kullanıcı ile ilişki
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}