<?php

namespace Anil\Dump\Context;

use Illuminate\Http\Request;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\ContextProvider\ContextProviderInterface;

final class RequestContextProvider implements ContextProviderInterface
{
    private readonly VarCloner $cloner;

    public function __construct(private readonly ?Request $request = null)
    {
        $this->cloner = new VarCloner;
        $this->cloner->setMaxItems(0);
    }

    /** @return array<string, mixed>|null */
    public function getContext(): ?array
    {
        if ($this->request === null) {
            return null;
        }

        return [
            'uri' => $this->request->getUri(),
            'method' => $this->request->getMethod(),
            'controller' => $this->cloner->cloneVar($this->resolveController()),
            'identifier' => spl_object_hash($this->request),
        ];
    }

    private function resolveController(): ?string
    {
        $route = $this->request?->route();

        if ($route === null) {
            return null;
        }

        if (is_object($route->controller)) {
            return class_basename($route->controller);
        }

        $uses = $route->action['uses'] ?? null;

        return match (true) {
            is_string($uses) => class_basename(strtok($uses, '@') ?: $uses),
            is_object($uses) => class_basename($uses),
            default => null,
        };
    }
}
