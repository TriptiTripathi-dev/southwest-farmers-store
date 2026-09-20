<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        // Checked BEFORE the app boots: individual tests use RefreshDatabase, which
        // runs migrate:fresh. Against the shared production database that would
        // wipe it. phpunit.xml forces sqlite; this refuses to run otherwise.
        $connection = $_ENV['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?: null;
        if ($connection !== 'sqlite') {
            throw new \RuntimeException(
                'Refusing to run tests: DB_CONNECTION is "' . ($connection ?? 'unset') . '", expected "sqlite". '
                . 'Tests must never touch a real database.'
            );
        }

        parent::setUp();
    }
}
