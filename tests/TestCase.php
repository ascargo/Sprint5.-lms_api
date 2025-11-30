<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--force' => true]);

        $this->artisan('passport:keys', ['--force' => true]);

        $this->artisan('passport:client', [
            '--personal' => true,
            '--no-interaction' => true,
            '--provider' => 'users',
            '--name' => 'Testing Personal Client',
        ]);
    }
}
