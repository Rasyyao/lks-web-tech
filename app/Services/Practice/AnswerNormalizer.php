<?php

namespace App\Services\Practice;

class AnswerNormalizer
{
    /**
     * Normalize short answer string: lowercase, trim, collapse consecutive whitespaces.
     */
    public static function normalize(string $answer): string
    {
        $trimmed = trim($answer);
        $collapsed = preg_replace('/\s+/', ' ', $trimmed);

        return mb_strtolower($collapsed, 'UTF-8');
    }

    /**
     * Check if student answer matches any accepted normalized answer.
     *
     * @param  array<string>  $acceptedAnswers
     */
    public static function matches(string $studentAnswer, array $acceptedAnswers): bool
    {
        $normalizedStudent = self::normalize($studentAnswer);

        foreach ($acceptedAnswers as $accepted) {
            if (self::normalize($accepted) === $normalizedStudent) {
                return true;
            }
        }

        return false;
    }
}
