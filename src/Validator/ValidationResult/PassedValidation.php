<?php

namespace Bytepath\Shared\Validator\ValidationResult;

/**
 * A class that holds a list of validation errors.
 */
class PassedValidation extends ValidationResult
{
    /**
     * @param array $errors a key value stores of attributes with validation errors and a msg explaining the error
     */
    public function __construct(protected $data = [])
    {
        // Falsy values should be converted to array
        if(! $data) {
            $this->data = [];
        }
    }

    /**
     * Returns a key val list of data returned from validation
     * @return array|null|mixed an array by default, but can be anything
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Returns true if this class passes validation
     */
    public function passes(): bool
    {
        return true;
    }
}
