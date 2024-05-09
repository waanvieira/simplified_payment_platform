<?php

namespace App\Traits;

use App\Exceptions\MethodMagicException;

trait MethodsMagicsTrait
{

    public function __get($prop)
    {
        if (isset($this->{$prop})) {
            return $this->{$prop};
        }

        $className = get_class($this);
        throw new MethodMagicException("Property {$prop} not found in class {$className}");
    }

    public function id(): string
    {
        return (string) $this->id;
    }

    public function createdAt(): string
    {
        return (string)$this->createdAt->format('Y-m-d H:i:s');
    }
}
