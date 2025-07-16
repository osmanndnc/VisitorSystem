<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class visit extends Model
{
    use HasFactory;

    //Tablonun adı visits olmadığı için visit olarak belirtildi. Aksi takdirde laravel otomatik olarak Visits sanıp tabloyu bulamıyor.
     protected $table = 'visit';

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }
}
