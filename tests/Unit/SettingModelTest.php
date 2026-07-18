<?php

namespace Tests\Unit;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_set_and_get_value(): void
    {
        Setting::setValue('test_key', 'test_value');

        $this->assertEquals('test_value', Setting::getValue('test_key'));
    }

    public function test_get_value_returns_default(): void
    {
        $this->assertEquals('default', Setting::getValue('nonexistent', 'default'));
    }

    public function test_set_value_updates_existing(): void
    {
        Setting::setValue('key', 'value1');
        Setting::setValue('key', 'value2');

        $this->assertEquals('value2', Setting::getValue('key'));
    }

    public function test_get_mail_config_returns_defaults_when_not_set(): void
    {
        $config = Setting::getMailConfig();

        $this->assertIsArray($config);
        $this->assertEquals('smtp', $config['mailer']);
    }

    public function test_set_and_get_mail_config(): void
    {
        $mailConfig = [
            'mailer' => 'smtp',
            'smtp_host' => 'smtp.example.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
            'smtp_username' => 'user',
            'smtp_password' => 'pass',
            'from_address' => 'test@pch.com',
            'from_name' => 'Test',
            'admin_email' => 'admin@pch.com',
        ];

        Setting::setMailConfig($mailConfig);
        $retrieved = Setting::getMailConfig();

        $this->assertEquals('smtp', $retrieved['mailer']);
        $this->assertEquals('smtp.example.com', $retrieved['smtp_host']);
        $this->assertEquals('test@pch.com', $retrieved['from_address']);
    }
}
