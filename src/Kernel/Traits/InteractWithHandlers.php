<?php

declare(strict_types=1);

namespace EasyTiktok\Kernel\Traits;

use function array_reverse;
use function array_unshift;
use function call_user_func;
use Closure;
use EasyTiktok\Kernel\Exceptions\InvalidArgumentException;
use function func_get_args;
use function gettype;
use function is_array;
use function is_callable;
use function is_string;
use function method_exists;
use function spl_object_hash;

trait InteractWithHandlers
{
    /**
     * @var array<int, array{hash: string, handler: callable}>
     */
    protected array $handlers = [];

    /**
     * @return array<int, array{hash: string, handler: callable}>
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * @param callable|string $handler
     * @return InteractWithHandlers
     * @throws InvalidArgumentException
     */
    public function with(callable|string $handler): static
    {
        return $this->withHandler($handler);
    }

    /**
     * @param callable|string $handler
     * @return InteractWithHandlers
     * @throws InvalidArgumentException
     */
    public function withHandler(callable|string $handler): static
    {
        $this->handlers[] = $this->createHandlerItem($handler);

        return $this;
    }

    /**
     * @param  callable|string  $handler
     * @return array{hash: string, handler: callable}
     *
     * @throws InvalidArgumentException
     */
    public function createHandlerItem(callable|string $handler): array
    {
        return [
            'hash' => $this->getHandlerHash($handler),
            'handler' => $this->makeClosure($handler),
        ];
    }

    /**
     * @param callable|string $handler
     * @return string
     * @throws InvalidArgumentException
     */
    protected function getHandlerHash(callable|string $handler): string
    {
        switch (true) {
            case is_string($handler):
                return $handler;
            case is_array($handler):
                return is_string($handler[0]) ? $handler[0].'::'.$handler[1] : get_class($handler[0]).$handler[1];
            case $handler instanceof Closure:
                return spl_object_hash($handler);
            default:
                throw new InvalidArgumentException('Invalid handler: '.gettype($handler));
        }
    }

    /**
     * @param callable|string $handler
     * @return callable
     * @throws InvalidArgumentException
     */
    protected function makeClosure(callable|string $handler): callable
    {
        if (is_callable($handler)) {
            return $handler;
        }

        if (class_exists($handler) && method_exists($handler, '__invoke')) {
            /**
             * @psalm-suppress InvalidFunctionCall
             * @phpstan-ignore-next-line https://github.com/phpstan/phpstan/issues/5867
             */
            return fn () => (new $handler())(...func_get_args());
        }

        throw new InvalidArgumentException(sprintf('Invalid handler: %s.', $handler));
    }

    /**
     * @param callable|string $handler
     * @return InteractWithHandlers
     * @throws InvalidArgumentException
     */
    public function prepend(callable|string $handler): static
    {
        return $this->prependHandler($handler);
    }

    /**
     * @param callable|string $handler
     * @return InteractWithHandlers
     * @throws InvalidArgumentException
     */
    public function prependHandler(callable|string $handler): static
    {
        array_unshift($this->handlers, $this->createHandlerItem($handler));

        return $this;
    }

    /**
     * @param callable|string $handler
     * @return InteractWithHandlers
     * @throws InvalidArgumentException
     */
    public function without(callable|string $handler): static
    {
        return $this->withoutHandler($handler);
    }

    /**
     * @param callable|string $handler
     * @return InteractWithHandlers
     * @throws InvalidArgumentException
     */
    public function withoutHandler(callable|string $handler): static
    {
        $index = $this->indexOf($handler);

        if ($index > -1) {
            unset($this->handlers[$index]);
        }

        return $this;
    }

    /**
     * @param callable|string $handler
     * @return int
     * @throws InvalidArgumentException
     */
    public function indexOf(callable|string $handler): int
    {
        foreach ($this->handlers as $index => $item) {
            if ($item['hash'] === $this->getHandlerHash($handler)) {
                return $index;
            }
        }

        return -1;
    }

    /**
     * @param $value
     * @param callable|string $handler
     * @return InteractWithHandlers
     * @throws InvalidArgumentException
     */
    public function when($value, callable|string $handler): static
    {
        if (is_callable($value)) {
            $value = call_user_func($value, $this);
        }

        if ($value) {
            return $this->withHandler($handler);
        }

        return $this;
    }

    public function handle($result, $payload = null)
    {
        $next = $result = is_callable($result) ? $result : fn ($p) => $result;

        foreach (array_reverse($this->handlers) as $item) {
            $next = fn ($p) => $item['handler']($p, $next) ?? $result($p);
        }

        return $next($payload);
    }

    /**
     * @param callable|string $handler
     * @return bool
     * @throws InvalidArgumentException
     */
    public function has(callable|string $handler): bool
    {
        return $this->indexOf($handler) > -1;
    }
}
