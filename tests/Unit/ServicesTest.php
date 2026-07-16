<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Winner;
use App\Services\ActivityLogger;
use App\Services\CodeGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_code_generator_generates_unique_code(): void
    {
        $generator = app(CodeGenerator::class);
        $code = $generator->generateUniqueCode();

        $this->assertIsString($code);
        $this->assertEquals(10, strlen($code));
    }

    public function test_code_generator_avoids_ambiguous_characters(): void
    {
        $generator = app(CodeGenerator::class);
        $code = $generator->generateUniqueCode();

        $this->assertMatchesRegularExpression('/^[A-Z0-9]+$/', $code);
    }

    public function test_code_generator_ensures_database_uniqueness(): void
    {
        Winner::factory()->create(['unique_code' => 'TESTCODE12']);

        $generator = app(CodeGenerator::class);
        $code = $generator->generateUniqueCode();

        $this->assertNotEquals('TESTCODE12', $code);
    }

    public function test_activity_logger_logs_activity(): void
    {
        $user = User::factory()->create();
        $logger = app(ActivityLogger::class);

        $log = $logger->log('test_action', 'test_collection', null, $user->id, ['key' => 'value'], 'Test description');

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'test_action',
            'collection' => 'test_collection',
            'user_id' => $user->id,
            'description' => 'Test description',
        ]);

        $this->assertEquals('test_action', $log->action);
    }

    public function test_activity_logger_handles_null_user(): void
    {
        $logger = app(ActivityLogger::class);

        $log = $logger->log('system_action', 'system', null, null, null, 'System action');

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'system_action',
            'user_id' => null,
        ]);

        $this->assertNull($log->user_id);
    }
}
