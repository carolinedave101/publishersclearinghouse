<?php

namespace App\Jobs;

class ParaphraseHelper
{
    protected static array $synonyms = [
        'congratulations' => ['congrats', 'well done', 'amazing news', 'fantastic'],
        'won' => ['earned', 'received', 'been awarded', 'secured'],
        'prize' => ['award', 'reward', 'winnings', 'gift'],
        'excited' => ['thrilled', 'delighted', 'overjoyed', 'pleased'],
        'great' => ['wonderful', 'fantastic', 'excellent', 'outstanding'],
        'special' => ['exclusive', 'unique', 'limited', 'personal'],
        'offer' => ['opportunity', 'deal', 'proposal', 'invitation'],
        'claim' => ['redeem', 'collect', 'receive', 'accept'],
        'check' => ['review', 'view', 'see', 'look at'],
        'update' => ['notification', 'message', 'alert', 'information'],
        'important' => ['essential', 'crucial', 'key', 'significant'],
        'now' => ['today', 'right away', 'immediately', 'as soon as possible'],
    ];

    public static function paraphrase(string $text, int $variantIndex = 1): string
    {
        $text = self::applySynonymSubstitution($text, $variantIndex);
        $text = self::varyGreeting($text, $variantIndex);
        return $text;
    }

    protected static function applySynonymSubstitution(string $text, int $variantIndex): string
    {
        $wordsToReplace = max(2, min(5, count(self::$synonyms)));
        $shuffled = self::$synonyms;
        $keys = array_keys($shuffled);
        shuffle($keys);
        $selectedKeys = array_slice($keys, 0, $wordsToReplace);

        foreach ($selectedKeys as $key) {
            $alternatives = $shuffled[$key];
            $idx = ($variantIndex - 1) % count($alternatives);
            $text = preg_replace(
                '/\b' . preg_quote($key, '/') . '\b/i',
                $alternatives[$idx],
                $text,
                1
            );
        }

        return $text;
    }

    protected static function varyGreeting(string $text, int $variantIndex): string
    {
        $greetings = [
            1 => 'Dear {name},',
            2 => 'Hello {name},',
            3 => 'Hi {name},',
        ];

        $idx = (($variantIndex - 1) % count($greetings)) + 1;
        $text = preg_replace('/^Dear\s+\{name\},/i', $greetings[$idx], $text);

        return $text;
    }
}
