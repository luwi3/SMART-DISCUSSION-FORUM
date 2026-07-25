<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SpamDetectionService
{

    protected $llamaUrl;


    public function __construct()
    {
        $this->llamaUrl = "http://127.0.0.1:11434/api/generate";
    }



    public function isSpam($message)
    {

        $prompt = "

You are a spam detection system for an academic discussion forum.

Analyze the student's message.

Detect:
- scams
- phishing
- fake offers
- advertisements
- malicious links
- irrelevant promotional messages
- suspicious requests

Normal academic discussions are NOT spam.

Return ONLY JSON.

Format:

{
    \"spam\": true or false,
    \"reason\": \"short reason\"
}


Student message:

$message

";


        $response = Http::timeout(60)->post($this->llamaUrl,[

            "model" => "llama3.2:3b",

            "prompt" => $prompt,

            "stream" => false,

            "format" => "json",

            "options" => [

                "temperature" => 0.1,

                "num_predict" => 100

            ]

        ]);



        if(!$response->successful()){

            // If Llama fails,
            // allow the message instead of blocking users

            return false;

        }



        $result = $response->json();



        $aiResponse = json_decode(
            $result['response'] ?? '',
            true
        );



        if(!$aiResponse){

            return false;

        }



        // Convert AI string responses like "true"/"false"
        // into real PHP boolean values

        return filter_var(
            $aiResponse['spam'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

    }

}