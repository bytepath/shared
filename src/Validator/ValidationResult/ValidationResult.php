<?php

namespace Bytepath\Shared\Validator\ValidationResult;

use Bytepath\Shared\Validator\Exceptions\ValidationErrorDoesNotExistException;

abstract class ValidationResult
{
    /**
     * Returns true if validation passed, false if failed
     * @return bool
     */
    abstract public function passes(): bool;

    /**
     * Returns a key val list of errors and a message explaining the problem
     * @return array
     */
    public function getErrors(): array
    {
        return [];
    }

    /**
     * Returns a key val list of data returned from validation
     * @return array|null|mixed an array by default, but can be anything
     */
    public function getData(): mixed
    {
        return [];
    }

    /**
     * Returns the error message for the provided error
     * @param string $name a key in the list of errors
     * @throws ValidationErrorDoesNotExistException
     */
    public function getError($name): string
    {
        throw ValidationErrorDoesNotExistException::forName($name);
    }
}