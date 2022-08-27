<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departure extends Model
{
    use HasFactory;
    protected $table = 'departures';
    public $timestamps = false;
    protected $fillable = [
        'content_name',
        'status',
    ];
}
