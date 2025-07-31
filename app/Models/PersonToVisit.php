<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonToVisit extends Model
{
    use HasFactory;

    protected $table = 'person_to_visit';
    protected $fillable = ['person_name', 'unit_name', 'phone_number'];

}
