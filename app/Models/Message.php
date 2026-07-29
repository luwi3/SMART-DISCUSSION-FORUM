<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'body',
        'user_id',
        'group_id',
        'topic_id',
        'created_in_topic',
        'reply_to_message_id',
        'thread_id',
        'course_code',
        'ai_description',
        'deleted_by',
        'embedding',
    ];

    protected $casts = [
        'created_in_topic' => 'boolean',
        'embedding' => 'array',
    ];

    // Whole-message (not substring) matches — checked after lowercasing and
    // stripping punctuation, so a real question that merely starts with
    // "hey" or "why" never gets caught, only a message that IS just that.
    private const GREETING_PHRASES = [
        'hi', 'hii', 'hiii', 'hello', 'hey', 'heyy', 'yo', 'sup', 'howdy',
        'greetings', 'morning', 'afternoon', 'evening',
        'good morning', 'good afternoon', 'good evening', 'good night',
        'thanks', 'thank you', 'thnx', 'ty',
        'ok', 'okay', 'k', 'kk', 'sure', 'cool', 'nice',
        'yes', 'no', 'yep', 'nope', 'why', 'what', 'huh', 'hmm',
        'bye', 'goodbye', 'cya', 'see you',
        'lol', 'lmao', 'haha',

        // Greeting + audience — these clear the word/character check on
        // their own, so they need an exact-match entry to still get caught.
        'good morning everyone', 'good morning all', 'good morning guys', 'good morning class', 'good morning team',
        'good afternoon everyone', 'good afternoon all', 'good afternoon guys', 'good afternoon class',
        'good evening everyone', 'good evening all', 'good evening guys',
        'hi guys', 'hi everyone', 'hi all', 'hi class', 'hi team',
        'hey guys', 'hey everyone', 'hey all', 'hey class', 'hey team',
        'hello guys', 'hello everyone', 'hello all', 'hello class', 'hello team',
        'morning guys', 'morning everyone', 'morning all',
    ];

    /**
     * Used to decide whether a message is worth sending to the AI at all —
     * for embedding (EmbedMessage) and for topic classification
     * (ForumChatController's ProcessMessageAI dispatch guard). Blocks a
     * message if it's both short in word count AND thin in characters
     * (catches "good mng", "ok cool", "hey"), or if it's an exact greeting
     * phrase regardless of length (catches "good morning", which is long
     * enough to slip past the first check).
     */
    public static function isSubstantial(string $body): bool
    {
        $trimmed = trim($body);

        if ($trimmed === '') {
            return false;
        }

        if (str_word_count($trimmed) <= 2 && strlen($trimmed) < 9) {
            return false;
        }

        $normalized = trim(preg_replace('/[^\p{L}\p{N}\s]/u', '', strtolower($trimmed)));

        return !in_array($normalized, self::GREETING_PHRASES, true);
    }

    /**
     * The user who sent this message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The main message that started this thread
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'thread_id');
    }

    /**
     * All messages inside this thread
     */
    public function threadMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'thread_id');
    }

    /**
     * The exact message this message replied to
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_message_id');
    }

    /**
     * Direct replies under this message
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'reply_to_message_id');
    }

    /**
     * Study group relationship
     */
    public function groupDiscussion(): BelongsTo
    {
        return $this->belongsTo(GroupDiscussion::class, 'group_id');
    }

    /**
     * Topic/course discussion relationship
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }
}