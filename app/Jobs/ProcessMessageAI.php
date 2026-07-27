<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\TopicService;
use App\Events\TopicAssigned;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMessageAI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $messageId;


    /**
     * Create a new job instance.
     */
    public function __construct($messageId)
    {
        $this->messageId = $messageId;
        $this->onQueue('ai-classification');
    }


    /**
     * Execute the job.
     */
    public function handle(TopicService $topicService)
    {

        // Get the original message
        $message = Message::find($this->messageId);


        if (!$message) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate AI topic creation
        |--------------------------------------------------------------------------
        */

        if ($message->topic_id) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Run AI processing
        |--------------------------------------------------------------------------
        */

        $topicService->processNewMessage($message);



        /*
        |--------------------------------------------------------------------------
        | Refresh message after TopicService updates it
        |--------------------------------------------------------------------------
        */

        $message->refresh();



        /*
        |--------------------------------------------------------------------------
        | Notify all users that topic was assigned
        |--------------------------------------------------------------------------
        */

        event(new TopicAssigned($message));


    }
}