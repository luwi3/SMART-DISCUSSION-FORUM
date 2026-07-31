<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\EmbeddingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EmbedMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

        if (!Message::isSubstantial($message->body)) {
            return;
        }

        // If the embedding server is unreachable, skip quietly instead of
        // retrying/failing loudly — same fallback as ProcessMessageAI.
        try {
            $message->embedding = $embeddingService->createEmbedding($message->body);
            $message->save();
        } catch (\Throwable $e) {
            Log::warning('Message embedding skipped: ' . $e->getMessage(), [
                'message_id' => $this->messageId,
            ]);
        }
    }
}m
