<?php

declare(strict_types=1);

namespace EasyTiktok\Kernel\Traits;

use function array_key_exists;
use function array_merge;
use function json_encode;

trait HasAttributes
{
    /**
     * @var  array<int|string,mixed>
     */
    protected array $attributes = [];

    /**
     * @param  array<int|string,mixed>  $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array<int|string,mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    public function toJson(): string|false
    {
        return json_encode($this->attributes);
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * @param  array<int|string,mixed>  $attributes
     */
    public function merge(array $attributes): self
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * @return  array<int|string,mixed>  $attributes
     */
    public function jsonSerialize(): array
    {
        return $this->attributes;
    }

    public function __set(string $attribute, $value): void
    {
        $this->attributes[$attribute] = $value;
    }

    public function __get(string $attribute)
    {
        return $this->attributes[$attribute] ?? null;
    }

    public function offsetExists($offset): bool
    {
        /** @phpstan-ignore-next-line */
        return array_key_exists($offset, $this->attributes);
    }

    public function offsetGet($offset)
    {
        return $this->attributes[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if (null === $offset) {
            $this->attributes[] = $value;
        } else {
            $this->attributes[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->attributes[$offset]);
    }
}
