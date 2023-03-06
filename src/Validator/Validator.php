<?php

namespace Bytepath\Shared\Validator;

/**
 * An implementation of ValidatorInterface that shows how I typically use this interface. Extend this class within your
 * framework, implementing the check() method. this method should do the validation in the native framework way.
 */

use Bytepath\Shared\Validator\ValidationResult\FailedValidation;
use Bytepath\Shared\Validator\ValidationResult\PassedValidation;
use Bytepath\Shared\Validator\ValidationResult\ValidationResult;
use Bytepath\Shared\Validator\Exceptions\ValidationException;
use Bytepath\Shared\Validator\Interfaces\ValidatorInterface;
use Closure;

abstract class Validator implements ValidatorInterface
{
    /**
     * The list of validation rules that data must satisfy
     * @var null
     */
    protected $ruleList = null;

    public function __construct($rules)
    {
        $this->ruleList = $rules;
    }

    /**
     * Returns a new instance of this validator loaded up with the provided $rules
     * @param $rules
     * @return $this
     */
    public function rules($rules): self
    {
        return new static($rules);
    }


    /**
     * Ensures that the list of rules in this validator are ok. If rules fail we throw a ValidationException
     * @param $rules the list of rules in this validator
     * @throws ValidationException
     */
    protected function checkRules($rules)
    {
        if($rules === null) {
            throw ValidationException::invalidRules($this);
        }

        if(empty($rules)) {
            throw ValidationException::emptyRules($this);
        }
    }

    /**
     * Filters any data that does not have a rule we can evaluate against
     * @param $data
     * @param $rules
     */
    protected function filterDataWithoutRules($data, $rules)
    {
        return array_intersect_key($data, $this->ruleList);
    }

    /**
     * Run the provided
     * @return ValidationResult retuns a PassedValidation if OK and a FailedValidation if not OK
     */
    abstract protected function checkData($data): ValidationResult;

    /**
     * Validate the provided data, and if passes, run the provided closure function.
     *
     * @param array $data
     * @param Closure|null $callback
     * @return ValidationResult
     * @throws ValidationException
     */
    public function validate($data, ?Closure $callback = null): ValidationResult
    {
        // Ensure we have a valid list of rules
        $this->checkRules($this->ruleList);

        // Throw away any data that doesnt have rules
        $filteredData = $this->filterDataWithoutRules($data, $this->ruleList);

        // Validate the provided data
        $validated = $this->checkData($filteredData);

        // If validation failed we can just return now
        if($validated instanceof FailedValidation) {
            return $validated;
        }

        // If a callback to run on validation success has been provided, do that now. The value returned by the closure
        // should be added to the PassedValidation object we are going to return
        if($callback !== null) {
            return new PassedValidation($callback($filteredData));
        }

        return new PassedValidation();
    }
}
