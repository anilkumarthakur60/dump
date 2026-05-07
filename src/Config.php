<?php

namespace Anil\Dump;

use Illuminate\Contracts\Config\Repository;

final readonly class Config
{
    public function __construct(
        public bool $enabled,
        public string $host,
        public int $maxDepth,
        public int $maxItems,
        public bool $logEnabled,
        public string $logChannel,
        public string $logLevel,
    ) {}

    public static function fromRepository(Repository $config): self
    {
        return new self(
            enabled: (bool) $config->get('dump-server.enabled', true),
            host: self::asString($config->get('dump-server.host'), 'tcp://127.0.0.1:9912'),
            maxDepth: self::asInt($config->get('dump-server.max_depth'), 10),
            maxItems: self::asInt($config->get('dump-server.max_items'), 2500),
            logEnabled: (bool) $config->get('dump-server.log.enabled', false),
            logChannel: self::asString($config->get('dump-server.log.channel'), 'stack'),
            logLevel: self::asString($config->get('dump-server.log.level'), 'debug'),
        );
    }

    private static function asString(mixed $value, string $default): string
    {
        return is_string($value) && $value !== '' ? $value : $default;
    }

    private static function asInt(mixed $value, int $default): int
    {
        return is_int($value) || (is_string($value) && is_numeric($value)) ? (int) $value : $default;
    }
}
