<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitReason extends Model
{
    use HasFactory;

    protected $table = 'visit_reasons';
    protected $fillable = ['reason','is_active'];

}
