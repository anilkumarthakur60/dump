<?php

if (! function_exists('dump_if')) {
    function dump_if(bool $condition, mixed ...$values): void
    {
        if ($condition) {
            dump(...$values);
        }
    }
}

if (! function_exists('dump_unless')) {
    function dump_unless(bool $condition, mixed ...$values): void
    {
        if (! $condition) {
            dump(...$values);
        }
    }
}

if (! function_exists('dd_if')) {
    function dd_if(bool $condition, mixed ...$values): void
    {
        if ($condition) {
            dd(...$values);
        }
    }
}

if (! function_exists('dd_unless')) {
    function dd_unless(bool $condition, mixed ...$values): void
    {
        if (! $condition) {
            dd(...$values);
        }
    }
}
