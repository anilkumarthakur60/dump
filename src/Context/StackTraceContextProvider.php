<?php

namespace Anil\Dump\Context;

use Symfony\Component\VarDumper\Dumper\ContextProvider\ContextProviderInterface;

class StackTraceContextProvider implements ContextProviderInterface
{
    /** @return array<string, mixed>|null */
    public function getContext(): ?array
    {
        $frames = [];

        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $frame) {
            $file = $frame['file'] ?? '';

            if (empty($file) || str_contains($file, '/vendor/symfony/var-dumper/')) {
                continue;
            }

            $frames[] = [
                'file' => $file,
                'line' => $frame['line'] ?? 0,
                'function' => ($frame['class'] ?? '').($frame['type'] ?? '').$frame['function'],
            ];

            if (count($frames) >= 5) {
                break;
            }
        }

        return ['stack' => $frames];
    }
}
