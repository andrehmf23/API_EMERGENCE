<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//Mostra os campos que podem ser preenchidos na tabela Clima
class Clima extends Model
{
    use HasFactory;
    protected $fillable = [
        'pais',
        'latitude',
        'longitude',
        'weather_data',
    ];
}


