<?php

use Tests\TestCase;

uses(TestCase::class)->in('Feature');

afterEach(fn () => Mockery::close());
