<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LlamaService
{

    protected $llamaUrl;


    public function __construct()
    {
        $this->llamaUrl = "http://127.0.0.1:11434/api/generate";
    }



    public function generateTopic($message)
    {

        $prompt = "

You are an AI assistant for an academic discussion forum.

Analyze the student's message and create a discussion topic.

Generate:

1. A short topic title.
2. A concise academic description.

Rules:

- The title must be at most 8 words.
- The description must be at most 12 words.
- Combined total words MUST NOT exceed 28words.
- The description must explain the main idea briefly.
- Include important academic keywords related to the topic.
- Do not mention AI.
- Do not add explanations.
- Do not use markdown.
- Return ONLY JSON.

The response must start with { and end with }.

Student message:

$message


Return exactly this format:

{
    \"title\": \"Short topic title here\",
    \"description\": \"Concise academic description under 20 words\"
}

";


        $response = Http::timeout(180)->post($this->llamaUrl, [

            "model" => "llama3.2:3b",

            "prompt" => $prompt,

            "stream" => false,

            "format" => "json",

            "options" => [

                "temperature" => 0.4,

                "num_predict" => 125// Increased token safety headroom so JSON closes cleanly

            ]

        ]);



        if (!$response->successful()) {

            throw new \Exception(
                "Llama request failed: ".$response->body()
            );

        }



        $result = $response->json();


        $cleanResponse = trim($result['response']);


        $cleanResponse = str_replace(
            [
                "```json",
                "```"
            ],
            "",
            $cleanResponse
        );


        $aiData = json_decode(
            trim($cleanResponse),
            true
        );



        if (!$aiData) {

            throw new \Exception(
                "Llama returned invalid JSON: ".$result['response']
            );

        }



        if (
            !isset($aiData['title']) ||
            !isset($aiData['description'])
        ) {

            throw new \Exception(
                "Llama JSON missing required fields"
            );

        }



        // PHP Hard Enforcer: Limits Title to 8 words & Description to 12 words (Max 20 total)
        return [

            'title' => Str::words(trim($aiData['title']), 8, ''),

            'description' => Str::words(trim($aiData['description']), 20, '')

        ];

    }

}