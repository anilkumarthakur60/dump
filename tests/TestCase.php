<?php

namespace Tests;

use Anil\Dump\DumpServerServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [DumpServerServiceProvider::class];
    }
}
