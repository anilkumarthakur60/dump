<?php

namespace Anil\Dump\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Command\Descriptor\CliDescriptor;
use Symfony\Component\VarDumper\Command\Descriptor\DumpDescriptorInterface;
use Symfony\Component\VarDumper\Command\Descriptor\HtmlDescriptor;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Server\DumpServer;

class DumpServerCommand extends Command
{
    private const PALETTE = ['blue', 'green', 'yellow', 'magenta', 'cyan', 'red'];

    protected $signature = 'dump:server {--format=cli : The output format (cli, html).}';

    protected $description = 'Start the dump server to collect dump output.';

    private int $count = 0;

    public function __construct(private readonly DumpServer $server)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $descriptor = $this->descriptorFor(is_string($this->option('format')) ? $this->option('format') : 'cli');

        $io = new SymfonyStyle($this->input, $this->output);
        $errorIo = $io->getErrorStyle();

        $errorIo->title('Laravel Var Dump Server');
        $this->server->start();
        $errorIo->success(sprintf('Server listening on %s', $this->server->getHost()));
        $errorIo->comment('Quit the server with CONTROL-C.');

        $this->server->listen(fn (Data $data, array $context, int $clientId) => $this->onDump($descriptor, $io, $data, $context, $clientId));
    }

    private function descriptorFor(string $format): DumpDescriptorInterface
    {
        return match ($format) {
            'cli' => new CliDescriptor(new CliDumper),
            'html' => new HtmlDescriptor(new HtmlDumper),
            default => throw new InvalidArgumentException(sprintf('Unsupported format "%s".', $format)),
        };
    }

    /** @param array<array-key, mixed> $context */
    private function onDump(DumpDescriptorInterface $descriptor, SymfonyStyle $io, Data $data, array $context, int $clientId): void
    {
        $color = self::PALETTE[$this->count % count(self::PALETTE)];
        $this->count++;
        $label = " DUMP #{$this->count} ";
        $bar = str_repeat('─', max(0, (new Terminal)->getWidth() - mb_strlen($label)));

        $io->writeln('');
        $io->writeln(sprintf('<bg=%s;fg=white;options=bold>%s%s</>', $color, $label, $bar));
        $descriptor->describe($io, $data, $context, $clientId);
    }
}
