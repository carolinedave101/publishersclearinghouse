<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Winner;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'winner_id' => Winner::factory(),
            'document_type' => fake()->randomElement(['government_id', 'proof_of_address', 'tax_form_w9', 'bank_information', 'signed_agreement']),
            'custom_type' => null,
            'status' => 'requested',
            'admin_notes' => null,
            'submitted_at' => null,
            'verified_at' => null,
        ];
    }
}
