<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\User; // 👤 Imported to find users to notify
use App\Notifications\NewTopicNotification; // 🔔 Imported your notification class
use Illuminate\Support\Facades\Notification;
use App\Services\LlamaService;
use App\Services\EmbeddingService;
use App\Services\RecommendationService;

class TopicController extends Controller
{
    protected $llamaService;
    protected $embeddingService;
    protected $recommendationService;

    public function __construct(
        LlamaService $llamaService,
        EmbeddingService $embeddingService,
        RecommendationService $recommendationService
    ) {
        $this->llamaService = $llamaService;
        $this->embeddingService = $embeddingService;
        $this->recommendationService = $recommendationService;
    }

    public function create()
    {
        return view('topics.create');
    }

    /**
     * Up to 5 topics matched to the student's own embedded message history,
     * for the "Recommend Topics" button on the dashboard.
     */
    public function recommended(Request $request)
    {
        $topics = $this->recommendationService->recommendTopicsForStudent(auth()->user(), 5);

        return response()->json(['topics' => $topics]);
    }

    /**
     * Up to 10 topics matched to an explicit search phrase, for the
     * "Search Topics" button — discovery on demand rather than passive.
     */
    public function searchRecommend(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|max:255',
        ]);

        $topics = $this->recommendationService->searchTopics($validated['query'], 10);

        return response()->json(['topics' => $topics]);
    }

    public function store(Request $request)
    {
        // 1. Validate 'title' and 'description' with strong input constraints
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // 2. Llama adds academic context to the title/description, capped at
        // 30 words total, then that enriched text is embedded so the topic
        // can be matched against related discussions.
        $contextText = $this->llamaService->addContextToTopic(
            $validated['title'],
            $validated['description']
        );

        $embedding = $this->embeddingService->createEmbedding($contextText);

        // 3. Create and store the topic record in $topic for notifications
        $topic = Topic::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'embedding' => $embedding,
            'user_id' => auth()->id(),
        ]);

        // 4. 🔔 NOTIFICATION SYSTEM: Get all users except the creator
        $usersToNotify = User::where('id', '!=', auth()->id())->get();

        // Send the notification via database + Reverb broadcast pipeline
        Notification::send($usersToNotify, new NewTopicNotification($topic));

        // 5. Respond appropriately depending on how the request was made
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Topic created successfully!',
                'topic' => $topic
            ], 201);
        }

        // Normal browser form submission -> open the new topic's chat room
        // immediately so the student can start typing without an extra click
        return redirect()->route('chat.index', ['type' => 'topic', 'id' => $topic->id])
            ->with('success', 'Topic created successfully!');
    }
}