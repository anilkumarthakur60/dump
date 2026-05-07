<?php

namespace Anil\Dump;

use Psr\Log\LoggerInterface;

final readonly class DumpHandler
{
    public function __construct(
        private Dumper $dumper,
        private ?LoggerInterface $logger = null,
        private string $level = 'debug',
    ) {}

    public function __invoke(mixed $value): void
    {
        $this->dumper->dump($value);

        $this->logger?->log($this->level, 'dump: '.get_debug_type($value), ['value' => $value]);
    }
}
