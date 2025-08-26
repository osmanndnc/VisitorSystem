<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'is_active',
    ];

    /**
     * The persons that belong to the department.
     */
    public function persons()
    {
        return $this->belongsToMany(Person::class);
    }
    
    public function visitors()
    {
        return $this->hasMany(Visitor::class);
    }
}