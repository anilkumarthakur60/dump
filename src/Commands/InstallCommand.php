<?php

namespace Anil\Dump\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    protected $signature = 'dump:install';

    protected $description = 'Install the dump server configuration.';

    public function handle(): void
    {
        $this->callSilent('vendor:publish', ['--tag' => 'dump-server-config']);

        $this->appendEnvVariables();

        $this->components->info('Dump server installed successfully.');
        $this->newLine();
        $this->components->bulletList([
            'Config published to <comment>config/dump-server.php</comment>',
            'Run <comment>php artisan dump:server</comment> to start collecting dumps',
            'Use <comment>DUMP_SERVER_ENABLED=false</comment> to disable in an environment',
        ]);
    }

    private function appendEnvVariables(): void
    {
        $variables = <<<'ENV'

# Dump Server
DUMP_SERVER_HOST=tcp://127.0.0.1:9912
DUMP_SERVER_ENABLED=true
DUMP_SERVER_LOG_ENABLED=false
ENV;

        foreach ([base_path('.env'), base_path('.env.example')] as $file) {
            if (! file_exists($file)) {
                continue;
            }

            $contents = file_get_contents($file);

            if ($contents === false || Str::contains($contents, 'DUMP_SERVER_HOST')) {
                continue;
            }

            file_put_contents($file, $contents.$variables.PHP_EOL);
        }
    }
}
