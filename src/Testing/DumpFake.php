<?php

namespace Anil\Dump\Testing;

use PHPUnit\Framework\Assert;
use Symfony\Component\VarDumper\VarDumper;

final class DumpFake
{
    /** @var list<mixed> */
    private array $dumped = [];

    public static function fake(): self
    {
        $fake = new self;
        VarDumper::setHandler($fake->dump(...));

        return $fake;
    }

    public function dump(mixed $value): void
    {
        $this->dumped[] = $value;
    }

    public function assertDumped(mixed $expected, string $message = ''): void
    {
        Assert::assertTrue(
            $this->has(fn (mixed $item): bool => $item === $expected),
            $message ?: sprintf(
                'Failed asserting that [%s] was dumped. Dumped types: [%s].',
                get_debug_type($expected),
                implode(', ', array_map(get_debug_type(...), $this->dumped))
            )
        );
    }

    public function assertNotDumped(mixed $unexpected, string $message = ''): void
    {
        Assert::assertFalse(
            $this->has(fn (mixed $item): bool => $item === $unexpected),
            $message ?: sprintf('Failed asserting that [%s] was not dumped.', get_debug_type($unexpected))
        );
    }

    public function assertNothingDumped(string $message = ''): void
    {
        Assert::assertEmpty(
            $this->dumped,
            $message ?: sprintf('Failed asserting nothing was dumped. Got [%d] dump(s).', count($this->dumped))
        );
    }

    public function assertDumpedCount(int $count, string $message = ''): void
    {
        Assert::assertCount(
            $count,
            $this->dumped,
            $message ?: sprintf('Failed asserting [%d] dumps. Got [%d].', $count, count($this->dumped))
        );
    }

    public function assertDumpedUsing(callable $callback, string $message = ''): void
    {
        Assert::assertTrue(
            $this->has($callback),
            $message ?: 'Failed asserting that a dump matched the given callback.'
        );
    }

    /** @return list<mixed> */
    public function getDumped(): array
    {
        return $this->dumped;
    }

    public function restore(): void
    {
        VarDumper::setHandler(null);
    }

    private function has(callable $matcher): bool
    {
        foreach ($this->dumped as $item) {
            if ($matcher($item)) {
                return true;
            }
        }

        return false;
    }
}
