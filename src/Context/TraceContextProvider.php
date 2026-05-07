<?php

namespace Anil\Dump\Context;

use Symfony\Component\VarDumper\Dumper\ContextProvider\ContextProviderInterface;

final class TraceContextProvider implements ContextProviderInterface
{
    private const VENDOR_PREFIX = '/vendor/symfony/var-dumper/';

    public function __construct(private readonly int $limit = 5) {}

    /** @return array<string, mixed> */
    public function getContext(): array
    {
        $frames = [];

        foreach (debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS) as $frame) {
            $file = $frame['file'] ?? '';

            if ($file === '' || str_contains($file, self::VENDOR_PREFIX)) {
                continue;
            }

            $frames[] = [
                'file' => $file,
                'line' => $frame['line'] ?? 0,
                'function' => ($frame['class'] ?? '').($frame['type'] ?? '').$frame['function'],
            ];

            if (count($frames) >= $this->limit) {
                break;
            }
        }

        return ['stack' => $frames];
    }
}
