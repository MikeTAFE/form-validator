<?php

/* 
  Install PHPUnit (if not already):
  composer require --dev phpunit/phpunit

  Run tests:
  ./vendor/bin/phpunit tests/FormValidatorTest.php
*/

use PHPUnit\Framework\TestCase;

final class FormValidatorTest extends TestCase
{
    private FormValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new FormValidator();
    }

    public function testRequiredFieldFails(): void
    {
        $data = ['name' => ''];
        $rules = ['name' => ['required']];
        $errors = $this->validator->validate($data, $rules);

        $this->assertArrayHasKey('name', $errors);
    }

    public function testRequiredFieldPasses(): void
    {
        $data = ['name' => 'John'];
        $rules = ['name' => ['required']];
        $errors = $this->validator->validate($data, $rules);

        $this->assertEmpty($errors);
    }

    public function testEmailValidation(): void
    {
        $data = ['email' => 'invalid-email'];
        $rules = ['email' => ['email']];
        $errors = $this->validator->validate($data, $rules);

        $this->assertArrayHasKey('email', $errors);
    }

    public function testIntValidation(): void
    {
        $data = ['age' => '25'];
        $rules = ['age' => ['int']];
        $errors = $this->validator->validate($data, $rules);

        $this->assertEmpty($errors);
    }

    public function testFloatValidation(): void
    {
        $data = ['price' => '19.99'];
        $rules = ['price' => ['float']];
        $errors = $this->validator->validate($data, $rules);

        $this->assertEmpty($errors);
    }

    public function testBooleanValidation(): void
    {
        $data = ['active' => 'yes'];
        $rules = ['active' => ['boolean']];
        $errors = $this->validator->validate($data, $rules);

        $this->assertArrayHasKey('active', $errors);

        $data = ['active' => '1'];
        $errors = $this->validator->validate($data, $rules);
        $this->assertEmpty($errors);
    }

    public function testDateValidation(): void
    {
        $data = ['birthday' => '2020-02-30']; // invalid date
        $rules = ['birthday' => ['date']];
        $errors = $this->validator->validate($data, $rules);

        $this->assertArrayHasKey('birthday', $errors);

        $data = ['birthday' => '2020-02-28']; // valid
        $errors = $this->validator->validate($data, $rules);
        $this->assertEmpty($errors);
    }

    public function testStringLengthValidation(): void
    {
        $data = ['username' => 'ab'];
        $rules = [
            'username' => [
                'min_length' => [3],
                'max_length' => [10]
            ]
        ];
        $errors = $this->validator->validate($data, $rules);

        $this->assertArrayHasKey('username', $errors);

        $data = ['username' => 'abcdef'];
        $errors = $this->validator->validate($data, $rules);
        $this->assertEmpty($errors);
    }

    public function testNumberRangeValidation(): void
    {
        $data = ['age' => '17'];
        $rules = ['age' => ['min' => [18]]];
        $errors = $this->validator->validate($data, $rules);
        $this->assertArrayHasKey('age', $errors);

        $data = ['age' => '99'];
        $rules = ['age' => ['max' => [65]]];
        $errors = $this->validator->validate($data, $rules);
        $this->assertArrayHasKey('age', $errors);
    }

    public function testRegexValidation(): void
    {
        $data = ['code' => 'ABC123'];
        $rules = ['code' => ['regex' => ['/^abc\d+$/i']]];
        $errors = $this->validator->validate($data, $rules);
        $this->assertEmpty($errors);

        $data = ['code' => '123ABC'];
        $errors = $this->validator->validate($data, $rules);
        $this->assertArrayHasKey('code', $errors);
    }

    public function testCustomErrorMessages(): void
    {
        $data = ['email' => 'bad-email'];
        $rules = [
            'email' => [
                'required' => 'Email is required.',
                'email' => 'Email must be valid.'
            ]
        ];
        $errors = $this->validator->validate($data, $rules);

        $this->assertEquals('Email must be valid.', $errors['email']);
    }

    public function testNestedFieldValidation(): void
    {
        $data = [
            'user' => [
                'profile' => ['age' => '16']
            ]
        ];
        $rules = [
            'user.profile.age' => [
                'required',
                'int',
                'min' => [18]
            ]
        ];
        $errors = $this->validator->validate($data, $rules);
        $this->assertArrayHasKey('user.profile.age', $errors);
    }
}
