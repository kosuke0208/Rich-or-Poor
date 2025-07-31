<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerNames extends Model
{
    use HasFactory;

    protected $table = 'playernames';
    protected $fillable = ['name'];
}
