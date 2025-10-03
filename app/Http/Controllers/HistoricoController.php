<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoricoPesquisa;
use Exception;

class HistoricoController extends Controller
{
    public function listar(){
        try{
            //busca todos os registros da tabela HistoricoPesquisa
            $registros = HistoricoPesquisa::all();

        //retorna um json com os registros da tabela HistoricoPesquisa
        return response()->json($registros);
        }catch (Exception $e) {
            return response()->json(['error' => 'Erro ao exibir o histórico', 'message' => $e->getMessage()], 500);
        }
    }
}
