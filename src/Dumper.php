<?php

namespace Anil\Dump;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Server\Connection;

class Dumper
{
    private readonly VarCloner $cloner;

    public function __construct(
        private readonly ?Connection $connection = null,
        private readonly int $maxDepth = 10,
        int $maxItems = 2500,
    ) {
        $this->cloner = new VarCloner;
        $this->cloner->setMaxItems($maxItems);
    }

    public function dump(mixed $value): void
    {
        $data = $this->cloner->cloneVar($value)->withMaxDepth($this->maxDepth);

        if ($this->connection?->write($data) === true) {
            return;
        }

        (\PHP_SAPI === 'cli' || \PHP_SAPI === 'phpdbg' ? new CliDumper : new HtmlDumper)->dump($data);
    }
}
