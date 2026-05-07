<?php

namespace Anil\Dump\Context;

use Illuminate\Http\Request;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\ContextProvider\ContextProviderInterface;

class RequestContextProvider implements ContextProviderInterface
{
    private VarCloner $cloner;

    public function __construct(private readonly ?Request $currentRequest = null)
    {
        $this->cloner = new VarCloner;
        $this->cloner->setMaxItems(0);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getContext(): ?array
    {
        if ($this->currentRequest === null) {
            return null;
        }

        $controller = null;

        if ($route = $this->currentRequest->route()) {
            $controller = $route->controller;

            if (! $controller && ! is_string($route->action['uses'])) {
                $controller = $route->action['uses'];
            }
        }

        $controllerName = (is_object($controller) || is_string($controller))
            ? $this->cloner->cloneVar(class_basename($controller))
            : $this->cloner->cloneVar(null);

        return [
            'uri' => $this->currentRequest->getUri(),
            'method' => $this->currentRequest->getMethod(),
            'controller' => $controllerName,
            'identifier' => spl_object_hash($this->currentRequest),
        ];
    }
}
