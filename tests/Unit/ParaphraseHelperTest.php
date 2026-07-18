<?php

namespace Tests\Unit;

use App\Jobs\ParaphraseHelper;
use Tests\TestCase;

class ParaphraseHelperTest extends TestCase
{
    public function test_paraphrase_returns_string(): void
    {
        $result = ParaphraseHelper::paraphrase('Congratulations! You have won a prize.', 1);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function test_paraphrase_replaces_congratulations(): void
    {
        $text = 'Congratulations! You are a winner.';
        $result = ParaphraseHelper::paraphrase($text, 1);
        $this->assertNotEquals($text, $result);
    }

    public function test_greeting_variation_variant_1(): void
    {
        $text = 'Dear {name}, welcome to PCH.';
        $result = ParaphraseHelper::paraphrase($text, 1);
        $this->assertStringContainsString('{name}', $result);
    }

    public function test_greeting_variation_variant_2(): void
    {
        $text = 'Dear {name}, welcome to PCH.';
        $result = ParaphraseHelper::paraphrase($text, 2);
        $this->assertStringContainsString('{name}', $result);
    }

    public function test_greeting_variation_variant_3(): void
    {
        $text = 'Dear {name}, welcome to PCH.';
        $result = ParaphraseHelper::paraphrase($text, 3);
        $this->assertStringContainsString('{name}', $result);
    }

    public function test_different_variants_produce_different_output(): void
    {
        $text = 'Congratulations! You have won a great prize.';

        $result1 = ParaphraseHelper::paraphrase($text, 1);
        $result2 = ParaphraseHelper::paraphrase($text, 2);

        $this->assertNotEquals($result1, $result2);
    }

    public function test_empty_text_returns_empty(): void
    {
        $result = ParaphraseHelper::paraphrase('', 1);
        $this->assertEquals('', $result);
    }

    public function test_text_without_synonyms_unchanged_structure(): void
    {
        $text = 'This is a simple test message.';
        $result = ParaphraseHelper::paraphrase($text, 1);
        $this->assertStringContainsString('simple', $result);
    }
}
