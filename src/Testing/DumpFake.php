<?php

namespace Anil\Dump\Testing;

use Anil\Dump\Dumper;
use PHPUnit\Framework\Assert;
use Symfony\Component\VarDumper\VarDumper;

final class DumpFake extends Dumper
{
    /** @var list<mixed> */
    private array $dumped = [];

    public static function fake(): self
    {
        $fake = new self;
        VarDumper::setHandler(function (mixed $var) use ($fake): void {
            $fake->dump($var);
        });

        return $fake;
    }

    public function dump(mixed $value): void
    {
        $this->dumped[] = $value;
    }

    public function assertDumped(mixed $expected, string $message = ''): void
    {
        $found = false;

        foreach ($this->dumped as $item) {
            if ($item === $expected) {
                $found = true;
                break;
            }
        }

        Assert::assertTrue($found, $message ?: sprintf(
            'Failed asserting that [%s] was dumped. Dumped types: [%s].',
            get_debug_type($expected),
            implode(', ', array_map(get_debug_type(...), $this->dumped))
        ));
    }

    public function assertNotDumped(mixed $unexpected, string $message = ''): void
    {
        $found = false;

        foreach ($this->dumped as $item) {
            if ($item === $unexpected) {
                $found = true;
                break;
            }
        }

        Assert::assertFalse($found, $message ?: sprintf(
            'Failed asserting that [%s] was not dumped.',
            get_debug_type($unexpected)
        ));
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
        $found = false;

        foreach ($this->dumped as $item) {
            if ($callback($item)) {
                $found = true;
                break;
            }
        }

        Assert::assertTrue($found, $message ?: 'Failed asserting that a dump matched the given callback.');
    }

    /** @return list<mixed> */
    public function getDumped(): array
    {
        return $this->dumped;
    }

    public function restore(): void
    {
        VarDumper::setHandler(null);
        $this->dumped = [];
    }
}
