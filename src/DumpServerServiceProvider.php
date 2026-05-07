<?php

namespace Anil\Dump;

use Anil\Dump\Commands\DumpServerCommand;
use Anil\Dump\Commands\InstallCommand;
use Anil\Dump\Context\RequestContextProvider;
use Anil\Dump\Context\StackTraceContextProvider;
use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Log\LogManager;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Symfony\Component\VarDumper\Server\Connection;
use Symfony\Component\VarDumper\Server\DumpServer;
use Symfony\Component\VarDumper\VarDumper;

class DumpServerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/dump-server.php' => config_path('dump-server.php'),
            ], ['config', 'dump-server-config']);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/dump-server.php', 'dump-server');

        $this->app->bind('command.dump-server', DumpServerCommand::class);
        $this->app->bind('command.dump-server.install', InstallCommand::class);
        $this->commands(['command.dump-server', 'command.dump-server.install']);

        /** @var Repository $config */
        $config = $this->app->make('config');

        if (! $config->get('dump-server.enabled', true)) {
            return;
        }

        $hostRaw = $config->get('dump-server.host', 'tcp://127.0.0.1:9912');
        $host = is_string($hostRaw) ? $hostRaw : 'tcp://127.0.0.1:9912';

        $maxDepthRaw = $config->get('dump-server.max_depth', 10);
        $maxDepth = match (true) {
            is_int($maxDepthRaw) => $maxDepthRaw,
            is_string($maxDepthRaw) => (int) $maxDepthRaw,
            default => 10,
        };

        $maxItemsRaw = $config->get('dump-server.max_items', 2500);
        $maxItems = match (true) {
            is_int($maxItemsRaw) => $maxItemsRaw,
            is_string($maxItemsRaw) => (int) $maxItemsRaw,
            default => 2500,
        };

        $logEnabled = (bool) $config->get('dump-server.log.enabled', false);

        $logChannelRaw = $config->get('dump-server.log.channel', 'stack');
        $logChannel = is_string($logChannelRaw) ? $logChannelRaw : 'stack';

        $logLevelRaw = $config->get('dump-server.log.level', 'debug');
        $logLevel = is_string($logLevelRaw) ? $logLevelRaw : 'debug';

        $this->app->when(DumpServer::class)->needs('$host')->give($host);

        if ($this->app->isProduction()) {
            return;
        }

        /** @var Request $request */
        $request = $this->app->make('request');

        $connection = new Connection($host, [
            'request' => new RequestContextProvider($request),
            'source' => new SourceContextProvider('utf-8', base_path()),
            'trace' => new StackTraceContextProvider,
        ]);

        VarDumper::setHandler(function (mixed $var) use ($connection, $maxDepth, $maxItems, $logEnabled, $logChannel, $logLevel): void {
            $this->app->makeWith(Dumper::class, [
                'connection' => $connection,
                'maxDepth' => $maxDepth,
                'maxItems' => $maxItems,
            ])->dump($var);

            if ($logEnabled) {
                /** @var LogManager $logger */
                $logger = $this->app->make('log');
                $logger->channel($logChannel)->log($logLevel, 'dump: '.get_debug_type($var), ['value' => $var]);
            }
        });
    }
}
