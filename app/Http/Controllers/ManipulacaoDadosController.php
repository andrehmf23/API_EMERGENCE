<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WeatherData;
use App\Models\HistoricoPesquisa;

use Exception;

class ManipulacaoDadosController extends Controller
{
    public function listar()
    {
        try {
            //retorna todos os dados registrados na tabela WeatherData
            $dados = WeatherData::all();

            //valida se exisste dado na tabela
            if ($dados->isEmpty()) {
                return response()->json(['message' => 'Nenhum dado encontrado'], 404);
            }

            //Adiciona na tabela a mensagem "Listando dados"
            historicoPesquisa::create([
                'message' => "Listando dados"
            ]);

            return response()->json($dados, 200);
        } catch (Exception $e) {
            //Até se deu erro para listar o dado, ele registra que deu erro, tudo isso para puxar o histórico de tudo o que foi realizado.
            historicoPesquisa::create([
                'message' => "Erro ao listar dados"
            ]);
            return response()->json(['error' => 'Erro ao listar dados', 'message' => $e->getMessage()], 500);
        }
    }

    public function deletar($nome)
    {
        try {
            //busca a cidade que o usuário deseja
            $dado = WeatherData::where('city_name', $nome)->first();

            //validação para caso não tiver registro da cidade que o usuário deseja
            if (!$dado) {
                return response()->json(['error' => 'Registro não encontrado'], 404);
            }

            //deleta o dado
            $dado->delete();

            //Na tabela historicoPesquisa cria uma mensagem falando que a cidade foi deletada
            historicoPesquisa::create([
                'message' => "Dado deletado da cidade $nome"
            ]);

            return response()->json(['message' => 'Registro deletado com sucesso!'], 200);
        } catch (Exception $e) {
            //registra o erro para deletar na tabela de historicoPesquisa
            historicoPesquisa::create([
                'message' => "Erro ao deletar dado da cidade {$nome}"
            ]);
            return response()->json(['error' => 'Erro ao deletar dados', 'message' => $e->getMessage()], 500);
        }
    }
}
