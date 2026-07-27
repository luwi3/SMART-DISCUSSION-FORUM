<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmbeddingService
{

    protected $embeddingUrl;


    public function __construct()
    {
        /*
        Ollama embedding API
        nomic-embed-text runs inside Ollama
        */

        $this->embeddingUrl = "http://127.0.0.1:11434/api/embeddings";
    }



    public function createEmbedding($text)
    {

        /*
        Send text to Ollama embedding server
        */

        $response = Http::post(
            $this->embeddingUrl,
            [
                'model' => 'nomic-embed-text',
                'prompt' => $text
            ]
        );



        /*
        Check if Ollama failed
        */

        if($response->failed())
        {
            throw new \Exception(
                "Ollama embedding server failed"
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
        ]

        */

        return $response->json()['embedding'];

    }

}