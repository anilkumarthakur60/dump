<?php

namespace Anil\Dump;

use Anil\Dump\Commands\DumpServerCommand;
use Anil\Dump\Commands\InstallCommand;
use Anil\Dump\Context\RequestContextProvider;
use Anil\Dump\Context\TraceContextProvider;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\LogManager;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Symfony\Component\VarDumper\Server\Connection;
use Symfony\Component\VarDumper\Server\DumpServer;
use Symfony\Component\VarDumper\VarDumper;

class DumpServerServiceProvider extends ServiceProvider
{
    private const CONFIG_PATH = __DIR__.'/../config/dump-server.php';

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([self::CONFIG_PATH => config_path('dump-server.php')], ['config', 'dump-server-config']);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG_PATH, 'dump-server');
        $this->commands([DumpServerCommand::class, InstallCommand::class]);

        $this->app->singleton(Config::class, function (Application $app): Config {
            /** @var ConfigRepository $repo */
            $repo = $app->make('config');

            return Config::fromRepository($repo);
        });

        $this->app->singleton(Connection::class, fn (Application $app): Connection => new Connection(
            $app->make(Config::class)->host,
            [
                'request' => new RequestContextProvider($app->make('request')),
                'source' => new SourceContextProvider('utf-8', base_path()),
                'trace' => new TraceContextProvider,
            ],
        ));

        $this->app->bind(Dumper::class, function (Application $app): Dumper {
            $config = $app->make(Config::class);

            return new Dumper($app->make(Connection::class), $config->maxDepth, $config->maxItems);
        });

        $this->app->when(DumpServer::class)
            ->needs('$host')
            ->give(fn (Application $app): string => $app->make(Config::class)->host);

        if ($this->app->isProduction() || ! $this->app->make(Config::class)->enabled) {
            return;
        }

        VarDumper::setHandler($this->makeHandler());
    }

    private function makeHandler(): DumpHandler
    {
        $config = $this->app->make(Config::class);
        $logger = null;

        if ($config->logEnabled) {
            /** @var LogManager $log */
            $log = $this->app->make('log');
            $logger = $log->channel($config->logChannel);
        }

        return new DumpHandler($this->app->make(Dumper::class), $logger, $config->logLevel);
    }
}
