<?php

namespace App\Services;

class SpamDetectionService
{
    /**
     * Known scam/phishing/promotional phrases that have no place in an
     * academic discussion forum.
     */
    protected array $spamPhrases = [
        'buy now', 'click here', 'click this link', 'limited time offer',
        'act now', 'act immediately', 'investment opportunity',
        'guaranteed profit', 'guaranteed return', 'risk free',
        'forex trading', 'crypto giveaway', 'bitcoin giveaway',
        'earn money fast', 'make money fast', 'work from home opportunity',
        'wire transfer', 'claim your prize', 'you have won', 'lottery winner',
        'no investment needed', 'double your money', 'dm me for',
        'whatsapp me at', 'text me at', 'call this number now',
        'subscribe to my channel', 'follow for follow', 'onlyfans',
        'casino bonus', 'free spins', 'sports betting tips',
    ];

    /**
     * Rule-based spam check — no external service, so it can't fail from
     * a dead network call. Scores several independent signals and flags
     * the message once they add up, rather than relying on any single
     * heuristic (which would be too easy to trigger on legitimate posts).
     */
    public function isSpam(string $message): bool
    {
        $normalized = strtolower(trim($message));

        if ($normalized === '') {
            return false;
        }

        $score = 0;

        foreach ($this->spamPhrases as $phrase) {
            if (str_contains($normalized, $phrase)) {
                $score += 3;
            }
        }

        $linkCount = preg_match_all('/https?:\/\/|www\./i', $message);
        if ($linkCount >= 2) {
            $score += 2;
        }

        // Excessive character repetition, e.g. "wowwwwwww" or "!!!!!!!!"
        if (preg_match('/(.)\1{5,}/', $message)) {
            $score += 1;
        }

        // Shouting: a longer message that's mostly uppercase letters
        $letters = preg_replace('/[^a-zA-Z]/', '', $message);
        if (strlen($letters) >= 15) {
            $uppercase = preg_replace('/[^A-Z]/', '', $letters);
            if (strlen($uppercase) / strlen($letters) > 0.7) {
                $score += 1;
            }
        }

        // A link paired with urgency/contact-me language is a strong signal
        if ($linkCount >= 1 && (
            str_contains($normalized, 'contact') ||
            str_contains($normalized, 'dm me') ||
            str_contains($normalized, 'whatsapp')
        )) {
            $score += 2;
        }

        return $score >= 3;
    }
}
