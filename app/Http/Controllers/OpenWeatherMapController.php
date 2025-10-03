<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\WeatherData;
use App\Models\HistoricoPesquisa;
use Exception;

class OpenWeatherMapController extends Controller
{
    public function getWeather(Request $request)
    {
        try {

            //Criei uma variável que armazena a chave da API e passei a minha chave fixa.
            $appid = "169440e27bd116f260e0d9afb46fc542";
            $name = $request->input('name');  //esse valor agora está vindo no corpo da requisição JSON

            //valida se o nome da cidade foi preenchido
            if (empty($name)) {
                return response()->json(['error' => 'Nome da cidade é obrigatório'], 400);
            }

            // Chama a API do Nominatim para obter as coordenadas, passando o nome da cidade como parâmetro
            $urlNominatim = "https://nominatim.openstreetmap.org/search?format=json&q={$name}";

            //Api Nominatim exige user-agent no cabeçalho da requisição
            // $response = Http::withHeaders([
            //     'User-Agent' => 'YourAppName/1.0 (https://yourwebsite.com)'
            // ])->get($urlNominatim);
            $response = Http::withOptions([
                'verify' => false, // 🔒 desativa a verificação SSL (somente em ambiente local)
            ])->withHeaders([
                'User-Agent' => 'YourAppName/1.0 (https://yourwebsite.com)'
            ])->get($urlNominatim);


            if ($response->failed()) {
                return response()->json(['error' => 'Erro ao obter coordenadas da cidade'], $response->status());
            }

            $nominatimData = $response->json();

            // Verifica se a resposta do Nominatim contém resultados válidos
            if (empty($nominatimData)) {
                return response()->json(['error' => 'Cidade não encontrada'], 404);
            }

            // Extrai a latitude e longitude da resposta do Nominatim
            $latitude = $nominatimData[0]['lat'];
            $longitude = $nominatimData[0]['lon'];

            //traz os dados da cidade pesquisada pelo usuário
            $weatherData = WeatherData::where('city_name', $name)->first();

            //Esse if valida se os dados da cidade já existe, caso existir ele faz o update no banco
            if ($weatherData) {
                $url = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid={$appid}&units=metric";
                $response = Http::get($url);

                if ($response->failed()) {
                    return response()->json(['error' => 'Erro ao obter dados do clima'], $response->status());
                }

                $data = $response->json();
                $weatherData->update([
                    'lat' => $data['coord']['lat'],
                    'lon' => $data['coord']['lon'],
                    'weather_main' => $data['weather'][0]['main'],
                    'weather_description' => $data['weather'][0]['description'],
                    'weather_icon' => $data['weather'][0]['icon'],
                    'temp' => $data['main']['temp'],
                    'feels_like' => $data['main']['feels_like'],
                    'temp_min' => $data['main']['temp_min'],
                    'temp_max' => $data['main']['temp_max'],
                    'pressure' => $data['main']['pressure'],
                    'humidity' => $data['main']['humidity'],
                    'visibility' => $data['visibility'],
                    'wind_speed' => $data['wind']['speed'],
                    'wind_deg' => $data['wind']['deg'],
                    'clouds_all' => $data['clouds']['all'],
                    'dt' => $data['dt'],
                    'country' => $data['sys']['country'],
                    'sunrise' => $data['sys']['sunrise'],
                    'sunset' => $data['sys']['sunset'],
                    'timezone' => $data['timezone']
                ]);

                //Cria a mensagem de atualização dos dados na tabela HistoricoPesquisa
                HistoricoPesquisa::create([
                    'message' => "Atualizado dado da cidade {$name}"
                ]);

                return response()->json(['message' => 'Dados atualizados com sucesso!', 'data' => $weatherData], 200);
            }

            // Se não houver dados, busque novos dados do clima e faz o insert a tabela
            $url = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid={$appid}&units=metric";
            $response = Http::withOptions([
                'verify' => false,
            ])->get($url);

            if ($response->failed()) {
                return response()->json(['error' => 'Erro ao obter dados do clima'], $response->status());
            }

            //realização do insert na tabela
            $data = $response->json();
            $weather = WeatherData::create([
                'lat' => $data['coord']['lat'],
                'lon' => $data['coord']['lon'],
                'weather_main' => $data['weather'][0]['main'],
                'weather_description' => $data['weather'][0]['description'],
                'weather_icon' => $data['weather'][0]['icon'],
                'temp' => $data['main']['temp'],
                'feels_like' => $data['main']['feels_like'],
                'temp_min' => $data['main']['temp_min'],
                'temp_max' => $data['main']['temp_max'],
                'pressure' => $data['main']['pressure'],
                'humidity' => $data['main']['humidity'],
                'visibility' => $data['visibility'],
                'wind_speed' => $data['wind']['speed'],
                'wind_deg' => $data['wind']['deg'],
                'clouds_all' => $data['clouds']['all'],
                'dt' => $data['dt'],
                'country' => $data['sys']['country'],
                'sunrise' => $data['sys']['sunrise'],
                'sunset' => $data['sys']['sunset'],
                'timezone' => $data['timezone'],
                'city_name' => $data['name']
            ]);

            //adiciona no HistoricoPesquisa a criação do dado na tabela
            HistoricoPesquisa::create([
                'message' => "Salvo dado da cidade {$name}"
            ]);

            return response()->json(['message' => 'Dados do clima salvos com sucesso!', 'data' => $weather], 201);
        } catch (Exception $e) {
            //Adiciona no HistoricoPesquisa o erro de salvar o dado
            HistoricoPesquisa::create([
                'message' => "Erro ao salvar dado da cidade {$name}"
            ]);
            return response()->json(['error' => 'Erro ao fazer contato com a API externa', 'message' => $e->getMessage()], 500);
        }
    }
}
