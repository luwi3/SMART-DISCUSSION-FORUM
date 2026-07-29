<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LlamaService
{

    protected $llamaUrl;


    public function __construct()
    {
        $this->llamaUrl = config('services.ollama.url', 'http://127.0.0.1:11434') . '/api/generate';
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
- The description must be at most 30 words.
- Combined total words MUST NOT exceed 38 words.
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
    \"description\": \"Concise academic description under 30 words\"
}

";


        $response = Http::timeout(180)->post($this->llamaUrl, [

            "model" => "llama3.2:3b",

            "prompt" => $prompt,

            "stream" => false,

            "format" => "json",

            "options" => [

                "temperature" => 0.3,

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



        // PHP Hard Enforcer: Limits Title to 8 words & Description to 30 words (Max 38 total)
        return [

            'title' => Str::words(trim($aiData['title']), 8, ''),

            'description' => Str::words(trim($aiData['description']), 30, '')

        ];

    }



    /**
     * Given a user-authored topic title + description, ask Llama to add
     * brief academic context and fold them into a single enriched
     * description (used as the source text for the topic's embedding).
     */
    public function addContextToTopic($title, $description)
    {

        $prompt = "

You are an AI assistant for an academic discussion forum.

A user has created a new discussion topic. Combine its title and
description into a single enriched description, adding brief academic
context or keywords that make the topic easier to match against related
discussions.

Rules:

- The combined output MUST NOT exceed 30 words.
- Add relevant academic context, do not just repeat the input verbatim.
- Do not mention AI.
- Do not add explanations.
- Do not use markdown.
- Return ONLY JSON.

The response must start with { and end with }.

Title:

$title

Description:

$description


Return exactly this format:

{
    \"context\": \"Enriched description here, at most 30 words\"
}

";

        $response = Http::timeout(180)->post($this->llamaUrl, [

            "model" => "llama3.2:3b",

            "prompt" => $prompt,

            "stream" => false,

            "format" => "json",

            "options" => [

                "temperature" => 0.3,

                "num_predict" => 100

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



        if (!$aiData || !isset($aiData['context'])) {

            throw new \Exception(
                "Llama returned invalid JSON: ".$result['response']
            );

        }



        // PHP Hard Enforcer: combined title + context must not exceed 30 words
        return Str::words(trim($aiData['context']), 30, '');

    }

}