<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('oauth_clients')) {
            Artisan::call('migrate', [
                '--path' => 'vendor/laravel/passport/database/migrations',
                '--realpath' => true,
                '--no-interaction' => true,
            ]);
        }

        Artisan::call('passport:keys', ['--force' => true]);

        Artisan::call('passport:client', [
            '--personal' => true,
            '--name' => 'Testing Personal Access Client',
            '--no-interaction' => true,
        ]);
    }
}
