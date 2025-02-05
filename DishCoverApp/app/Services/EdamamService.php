<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use App\Models\Grocery;
use Illuminate\Support\Facades\Auth;

class EdamamService
{
    protected $client;
    protected $appId;
    protected $appKey;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://api.edamam.com/']);
        $this->appId = env('EDAMAM_APP_ID');
        $this->appKey = env('EDAMAM_APP_KEY');
    }

    public function searchRecipes($query)
    {
        $url = 'https://api.edamam.com/search';

        $userId = Auth::id();
        $userIngredients = Grocery::where('user_id', $userId)->where('quantity', '>', 0)->pluck('ingredient_name')->toArray();
        $ingredientQuery = implode(',', $userIngredients);

        $combinedQuery = !empty($query) ? ($query . ', ' . $ingredientQuery) : $ingredientQuery;

        $params = [
            'q' => $combinedQuery,
            'app_id' => config('services.edamam.app_id'),
            'app_key' => config('services.edamam.api_key'),
            'from' => '0',
            'to' => '10',
        ];

        \Log::info('API Request', ['url' => $url, 'params' => $params]);

        $response = Http::get($url, $params);

        if ($response->successful()) {
            return $this->processResponse($response->json());
        }

        // - error handling -
        // return [
        //     'error' => 'Failed to fetch recipes',
        //     'status' => $response->status(),
        // ];
    }

    protected function processResponse($data)
    {
        return [
            'query' => $data['q'] ?? null,
            'from' => $data['from'] ?? null,
            'to' => $data['to'] ?? null,
            'more' => $data['more'] ?? null,
            'count' => $data['count'] ?? null,
            'hits' => $data['hits'] ?? [],
        ];
    }
}