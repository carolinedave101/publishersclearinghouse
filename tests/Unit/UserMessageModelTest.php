<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserMessageModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_message(): void
    {
        $msg = UserMessage::factory()->create();

        $this->assertDatabaseHas('user_messages', ['id' => $msg->id]);
    }

    public function test_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $msg = UserMessage::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($msg->user->is($user));
    }

    public function test_belongs_to_admin(): void
    {
        $admin = User::factory()->create();
        $msg = UserMessage::factory()->create(['admin_id' => $admin->id]);

        $this->assertTrue($msg->admin->is($admin));
    }

    public function test_unread_scope(): void
    {
        UserMessage::factory()->count(3)->create(['is_read' => false]);
        UserMessage::factory()->create(['is_read' => true]);

        $this->assertCount(3, UserMessage::unread()->get());
    }

    public function test_from_admin_scope(): void
    {
        UserMessage::factory()->count(2)->fromAdmin()->create();
        UserMessage::factory()->fromUser()->create();

        $this->assertCount(2, UserMessage::fromAdmin()->get());
    }

    public function test_from_user_scope(): void
    {
        UserMessage::factory()->fromUser()->create();
        UserMessage::factory()->count(2)->fromAdmin()->create();

        $this->assertCount(1, UserMessage::fromUser()->get());
    }

    public function test_casts(): void
    {
        $msg = UserMessage::factory()->create([
            'is_read' => 1,
            'read_at' => now(),
        ]);

        $this->assertTrue($msg->is_read);
        $this->assertInstanceOf(\Carbon\Carbon::class, $msg->read_at);
    }
}
