<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class RecommendationService
{
    // How many of the student's own most recent embedded messages feed the
    // interest profile — bounded for recency and to keep averaging cheap.
    private const PROFILE_MESSAGE_LIMIT = 50;

    protected $embeddingService;

    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
    }

    public function recommendTopicsForStudent(User $user, int $limit = 5): Collection
    {
        $profile = $this->buildStudentProfile($user);

        if (!$profile) {
            return $this->fallbackTopics($limit);
        }

        return $this->scoreTopicsAgainst($profile, $limit);
    }

    /**
     * Same relevance scoring as recommendTopicsForStudent(), but seeded by
     * an explicit search phrase instead of the student's message history —
     * for the "Search Topics" button, where the student already knows what
     * they're looking for rather than waiting to be matched passively.
     */
    public function searchTopics(string $query, int $limit = 10): Collection
    {
        $trimmed = trim($query);

        if ($trimmed === '') {
            return collect();
        }

        // If the embedding server is unreachable, fail quietly to an empty
        // result set instead of crashing the search request.
        try {
            $queryEmbedding = $this->embeddingService->createEmbedding($trimmed);
        } catch (\Throwable $e) {
            Log::warning('Topic search skipped (embedding unavailable): ' . $e->getMessage());
            return collect();
        }

        return $this->scoreTopicsAgainst($queryEmbedding, $limit);
    }

    private function scoreTopicsAgainst(array $vector, int $limit): Collection
    {
        return Topic::whereNotNull('embedding')
            ->get()
            ->map(fn (Topic $topic) => [
                'topic' => $topic,
                'similarity' => $this->cosineSimilarity($vector, $topic->embedding),
            ])
            ->sortByDesc('similarity')
            ->take($limit)
            ->map(fn (array $entry) => $this->formatTopic($entry['topic'], $entry['similarity']))
            ->values();
    }

    /**
     * Averages the student's own recent message embeddings into a single
     * profile vector. Null if they have no embedded messages yet (cold start
     * — see EmbedMessage, which never embeds trivial/short messages either).
     */
    private function buildStudentProfile(User $user): ?array
    {
        $vectors = Message::where('user_id', $user->id)
            ->whereNotNull('embedding')
            ->latest()
            ->take(self::PROFILE_MESSAGE_LIMIT)
            ->pluck('embedding');

        if ($vectors->isEmpty()) {
            return null;
        }

        return $this->averageVectors($vectors->all());
    }

    /**
     * Cold-start fallback for a student with no embedded messages yet —
     * surface the most active topics instead of a personalized match.
     */
    private function fallbackTopics(int $limit): Collection
    {
        return Topic::withCount('messages')
            ->orderByDesc('messages_count')
            ->take($limit)
            ->get()
            ->map(fn (Topic $topic) => $this->formatTopic($topic, null))
            ->values();
    }

    private function formatTopic(Topic $topic, ?float $similarity): array
    {
        // The topic's opening message — highlighted client-side so the
        // student immediately sees what the recommendation is about.
        $highlightMessage = Message::where('topic_id', $topic->id)
            ->orderBy('created_at', 'asc')
            ->first();

        return [
            'id' => $topic->id,
            'title' => $topic->title,
            'description' => $topic->description,
            'similarity' => $similarity,
            'highlight_message_id' => $highlightMessage?->id,
        ];
    }

    private function averageVectors(array $vectors): array
    {
        $dimensions = count($vectors[0]);
        $sum = array_fill(0, $dimensions, 0.0);

        foreach ($vectors as $vector) {
            foreach ($vector as $i => $value) {
                $sum[$i] += $value;
            }
        }

        $count = count($vectors);

        return array_map(fn ($value) => $value / $count, $sum);
    }

    private function cosineSimilarity($vectorA, $vectorB)
    {
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;

        foreach ($vectorA as $key => $value) {
            $dotProduct += $value * $vectorB[$key];
            $normA += $value * $value;
            $normB += $vectorB[$key] * $vectorB[$key];
        }

        if ($normA == 0 || $normB == 0) {
            return 0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
