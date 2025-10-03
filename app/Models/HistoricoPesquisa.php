<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//Mostra os campos que podem ser preenchidos na tabela HistoricoPesquisa
class HistoricoPesquisa extends Model
{
    use HasFactory;

    protected $table = 'historico_pesquisa'; // Nome da tabela

    protected $fillable = ['message']; // Campos permitidos para inserção
}
