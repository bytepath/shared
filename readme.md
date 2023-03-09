# Bytepath Shared Libraries

This package contains a bunch of libraries we use on every project. These libraries are framework agnostic in that you typically must provide an implementation for your framework of choice

## Validator

This is a framework agnostic library for performing validation on client data. The class provides both an interface that defines how the validator works, and an abstract implementation of that interface that you can use to implement validation in your application.

### ValidatorInterface

ValidatorInterface has two methods

```php
    public function rules($rules): self;

    public function validate($data, ?Closure $callback): ValidationResult;
```

**rules()**

The first method, rules, accepts a list of $rules and returns a new instance of whatever validator you are using. Rules are not currentlyy formally defined. It's up to you to decide what rules are. I use this with laravel, so 'rules' looks like this for me. In the future I plan to add some sort of rule definition class that makes your rules a bit more portable.

```php
$rules = [
  "name" => "required|string|max:100",
  "age" => "required|numeric",
];
```

this method returns a new class, leaving the original intact, so you can call this method at any time without having to worry about state etc.



**validate()**

The second method, validate, runs your rules against the provided $data. If all rules pass and the data is considered validated, the closure provided in the second argument will be ran. This closure will only run if validation passes, so you can do "dangerous" actions such as creating rows in the database etc, here.

If a value is returned from the closure, you can access this value with the getData() method of the ValidationResult returned by this method.


### ValidationResult

The validate method of the ValidatorInterface returns an object that extends the abstract ValidationResult class. Assuming you are using the premade Validator class in this library, there are two possible objects that can be returned.

**PassedValidation**

As the name suggests, this object will be returned if the provided data passed validation. This object will contain any data that you returned from the closure passed to the validate() method described above in the ValidatorInterface section. To access this data you can run the getData method of the PassedValidation class

**FailedValidation**

As the name suggests, this object will be returned if the provided data fails validation. This object will contain a key/val list of rules that did not pass as  well as a human readable string that you can provide to the user in your form. You can access this list of errors with the getErrors() method


### Extending PassedValidation and FailedValidation in your apps

Assuming the object returned by your app extends from ValidationResult, you can return whatever you want from your implementation of Validator. To make this process a bit simpler, the Validator class has two protected methods that you can override to change the class that will be returned in the event of pass/fail

```php
    protected function passed($data = []): PassedValidation
    protected function failed($errors = []): FailedValidation
```

If you extend either of these methods in your implementation you can change the value that gets returned. Values returned must extend Passed/FailedValidation respectively.

### Validator

Validator is an abstract class that does most of the work of implementing the ValidatorInterface for you. This class has one abstract method

```php
    abstract protected function checkData($data, $rules): ValidationResult;
```

That performs the validation action in the method of your choice. You must implement yourself.

This method returns an object that extends the ValidationResult class described above.
