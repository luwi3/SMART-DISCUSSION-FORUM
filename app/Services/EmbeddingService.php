<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmbeddingService
{

    protected $embeddingUrl;


    public function __construct()
    {
        /*
        URL of Python Flask embedding server
        */

        $this->embeddingUrl = config('services.embedding.url', 'http://127.0.0.1:5003') . '/embed';
    }





    public function createEmbedding($text)
    {

        /*
        Send text to Python server
        */

        $response = Http::timeout(10)->post(
            $this->embeddingUrl,
            [
                'text' => $text
            ]
        );



        /*
        Check if Python server failed
        */

        if($response->failed())
        {
            throw new \Exception(
                "Embedding server failed"
            );
        }




        /*
        Get embedding array

        Example:

        [
          0.061,
          0.038,
          -0.055,
          ...
          384 values
        ]

        */

        return $response->json()['embedding'];

    }

}