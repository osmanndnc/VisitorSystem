<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;
    
    protected $table = 'visit';

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}