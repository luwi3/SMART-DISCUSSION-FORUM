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
use Illuminate\Support\Facades\Log;

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
        |
        | Wrapped in try/catch: if the Ollama / embedding servers are unreachable
        | (e.g. not running in this environment), we log it and move on quietly
        | instead of letting the exception bubble up. This matters especially
        | when QUEUE_CONNECTION=sync, since this job then runs inline inside the
        | same HTTP request that sent the chat message — an uncaught exception
        | here would otherwise turn "send a message" into a 500 error for the user.
        */

        try {
            $topicService->processNewMessage($message);

            // Refresh message after TopicService updates it
            $message->refresh();

            // Notify all users that topic was assigned
            event(new TopicAssigned($message));
        } catch (\Throwable $e) {
            Log::warning('AI topic processing skipped: ' . $e->getMessage(), [
                'message_id' => $this->messageId,
            ]);
        }
    }
}