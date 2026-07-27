<?php

namespace App\Services;

use App\Models\Topic;
use App\Models\Message;

class TopicService
{
    protected $embeddingService;
    protected $llamaService;


    public function __construct(
        EmbeddingService $embeddingService,
        LlamaService $llamaService
    ) {
        $this->embeddingService = $embeddingService;
        $this->llamaService = $llamaService;
    }



    public function processNewMessage(Message $message)
    {

        /*
        1. Send student's message to Llama
        Llama creates:
        - title
        - description
        */

        $aiData = $this->llamaService
                       ->generateTopic($message->body);



        /*
        2. Create text for embedding

        We DO NOT use title.

        Embedding contains:
        - original student message
        - AI generated description
        */

        $embeddingText =
            $message->body .
            "\n\n" .
            $aiData['description'];



        /*
        3. Generate embedding using nomic-embed-text model
        */

        $embedding = $this->embeddingService
                          ->createEmbedding($embeddingText);



        /*
        4. Find similar existing topic
        */

        $similarTopic = $this->findSimilarTopic($embedding);



        /*
        5. If similar topic exists,
        attach message to it
        */

        if ($similarTopic) {


            $message->topic_id = $similarTopic->id;
            $message->ai_description = $aiData['description'];

            $message->save();


            return $similarTopic;

        }



        /*
        6. No similar topic found.
        Create a new topic.
        */


        $topic = Topic::create([

            'title' => $aiData['title'],

            'description' => $aiData['description'],

            'embedding' => $embedding,

            'user_id' => $message->user_id

        ]);



        /*
        7. Attach message to new topic
        */


        $message->topic_id = $topic->id;
        $message->ai_description = $aiData['description'];

        $message->save();



        return $topic;

    }







    private function findSimilarTopic($newEmbedding)
    {

        /*
        Before comparison,
        create embeddings for old topics
        that don't have one.
        */

        $this->createMissingEmbeddings();



        /*
        Get only topics that have embeddings
        */

        $topics = Topic::whereNotNull('embedding')
                       ->get();



        $bestTopic = null;

        $highestSimilarity = 0;



        foreach ($topics as $topic) {


            $similarity = $this->cosineSimilarity(
                $newEmbedding,
                $topic->embedding
            );



            if ($similarity > $highestSimilarity) {


                $highestSimilarity = $similarity;

                $bestTopic = $topic;

            }

        }



        /*
        Decision point

        >= 0.6
        means it belongs to an existing topic

        < 0.6
        means create new topic
        */


        if ($highestSimilarity >= 0.90) {

            return $bestTopic;

        }


        return null;

    }







   private function createMissingEmbeddings()
{

    $topics = Topic::whereNull('embedding')->get();


    foreach ($topics as $topic) {


        $text = 
            $topic->title .
            "\n\n" .
            $topic->description;


        $embedding = $this->embeddingService
                          ->createEmbedding($text);


        $topic->embedding = $embedding;

        $topic->save();

    }

}







    private function cosineSimilarity($vectorA, $vectorB)
    {

        $dotProduct = 0;

        $normA = 0;

        $normB = 0;



        foreach ($vectorA as $key => $value) {


            $dotProduct +=
                $value * $vectorB[$key];


            $normA +=
                $value * $value;


            $normB +=
                $vectorB[$key] * $vectorB[$key];

        }



        if ($normA == 0 || $normB == 0) {

            return 0;

        }



        return $dotProduct /
            (sqrt($normA) * sqrt($normB));

    }

}