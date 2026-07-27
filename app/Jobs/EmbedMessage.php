<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\EmbeddingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EmbedMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Below this many words a message is too thin to carry any real topic
    // signal ("hey", "thanks", "ok lol") — skip it rather than waste an
    // embedding-server call and dilute the student's interest profile.
    private const MIN_WORDS = 4;

    public $messageId;

    public function __construct($messageId)
    {
        $this->messageId = $messageId;
        $this->onQueue('embeddings');
    }

    public function handle(EmbeddingService $embeddingService)
    {
        $message = Message::find($this->messageId);

        if (!$message || $message->embedding) {
            return;
        }

        if (str_word_count(trim($message->body)) < self::MIN_WORDS) {
            return;
        }

        $message->embedding = $embeddingService->createEmbedding($message->body);
        $message->save();
    }
}
