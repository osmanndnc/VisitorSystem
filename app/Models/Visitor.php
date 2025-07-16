<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Visitor extends Model
{
    use HasFactory;

    // Ziyaretçiyi onaylayan kullanıcı ile ilişki
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}