<?php

namespace Anil\Dump\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Command\Descriptor\CliDescriptor;
use Symfony\Component\VarDumper\Command\Descriptor\HtmlDescriptor;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Server\DumpServer;

class DumpServerCommand extends Command
{
    protected $signature = 'dump:server {--format=cli : The output format (cli, html).}';

    protected $description = 'Start the dump server to collect dump output.';

    public function __construct(private readonly DumpServer $server)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $option = $this->option('format');
        $format = is_string($option) ? $option : 'cli';

        $descriptor = match ($format) {
            'cli' => new CliDescriptor(new CliDumper),
            'html' => new HtmlDescriptor(new HtmlDumper),
            default => throw new InvalidArgumentException(sprintf('Unsupported format "%s".', $format)),
        };

        $io = new SymfonyStyle($this->input, $this->output);
        $errorIo = $io->getErrorStyle();

        $errorIo->title('Laravel Var Dump Server');
        $this->server->start();
        $errorIo->success(sprintf('Server listening on %s', $this->server->getHost()));
        $errorIo->comment('Quit the server with CONTROL-C.');

        $dumpCount = 0;
        $palette = ['blue', 'green', 'yellow', 'magenta', 'cyan', 'red'];

        $this->server->listen(function (Data $data, array $context, int $clientId) use ($descriptor, $io, $palette, &$dumpCount): void {
            $color = $palette[$dumpCount % count($palette)];
            $dumpCount++;
            $width = (new Terminal)->getWidth();
            $label = " DUMP #{$dumpCount} ";
            $bar = str_repeat('─', max(0, $width - mb_strlen($label)));
            $io->writeln('');
            $io->writeln(sprintf('<bg=%s;fg=white;options=bold>%s%s</>', $color, $label, $bar));
            $descriptor->describe($io, $data, $context, $clientId);
        });
    }
}
