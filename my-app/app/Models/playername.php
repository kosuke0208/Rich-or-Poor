<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playername extends Model
{
    use HasFactory;

    protected $table = 'playernames';
    protected $fillable = ['name'];
    protected $dates =  ['created_at', 'updated_at'];


}
