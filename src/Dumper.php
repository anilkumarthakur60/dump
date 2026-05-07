<?php

namespace Anil\Dump;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Server\Connection;

class Dumper
{
    public function __construct(
        private readonly ?Connection $connection = null,
        private readonly int $maxDepth = 10,
        private readonly int $maxItems = 2500,
    ) {}

    public function dump(mixed $value): void
    {
        $data = $this->createVarCloner()->cloneVar($value)->withMaxDepth($this->maxDepth);

        if ($this->connection !== null && $this->connection->write($data) !== false) {
            return;
        }

        $dumper = in_array(PHP_SAPI, ['cli', 'phpdbg']) ? new CliDumper : new HtmlDumper;
        $dumper->dump($data);
    }

    protected function createVarCloner(): VarCloner
    {
        $cloner = new VarCloner;
        $cloner->setMaxItems($this->maxItems);

        return $cloner;
    }
}
