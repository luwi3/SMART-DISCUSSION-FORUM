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

            $this->moveThreadToTopic($message, $similarTopic);


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

        $this->moveThreadToTopic($message, $topic);



        return $topic;

    }




    /**
     * Every reply anywhere under this root carries thread_id = root's own
     * id (see ForumChatController::store()), no matter how deep the reply
     * chain goes. So the whole conversation moves with the root message —
     * same reassignment, same rows, same senders/timestamps/content,
     * nothing recreated. withTrashed() so a deleted reply stays correctly
     * grouped with its thread instead of being left behind in main chat.
     */
    private function moveThreadToTopic(Message $rootMessage, Topic $topic): void
    {
        Message::withTrashed()
            ->where('thread_id', $rootMessage->id)
            ->update(['topic_id' => $topic->id]);
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

        >= 0.5
        means it belongs to an existing topic

        < 0.5
        means create new topic
        */


        if ($highestSimilarity >= 0.5) {

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