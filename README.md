# FormValidator

A lightweight, reusable, and extensible PHP 8+ form validation class.  
Supports validating required fields, types (email, int, float, boolean, date), string lengths, number ranges, regex patterns, and nested arrays using dot notation.

---

## Features

- Required field validation
- Email validation
- Integer / float / boolean validation
- Date format (`Y-m-d`) validation
- String length (`min_length`, `max_length`)
- Number range (`min`, `max`)
- Custom regex support
- Dot notation for nested array keys
- Custom error messages per rule
- PHPUnit tests included

---

## Installation

The FormValidator library can be installed as a Composer package (via the GitHub repo) or just taken and used directly inside your project.

### 1. Using FormValidator directly (just stealing 1 file)

You can just copy and paste the main `FormValidator.php` class file into your own project to get up and running quickly, without worrying about Composer or other methods. This will give you the code, but won't enable you to easily update the library later on.

### 2. Installing as a Composer library

To install FormValidator as a Composer library, you can refer directly to this repo on GitHub.

```bash
composer require miketafe/form-validator:dev-main \
  --repository='{"type":"vcs", "url":"https://github.com/miketafe/form-validator"}'
```

> Uses PSR-4 autoloading with namespace `MikeTAFE`.

### 3. Cloning this project

If you want the entire repo, you can clone or download it manually and then use Composer autoloading:

```bash
git clone https://github.com/MikeTAFE/form-validator.git
cd form-validator

composer install
```

> Uses PSR-4 autoloading with namespace `MikeTAFE`.

---

## Directory structure

```
form-validator/
├── src/
│   └── FormValidator.php
├── tests/
│   └── FormValidatorTest.php
├── composer.json
└── README.md
```

---

## Usage example

```php
require 'vendor/autoload.php';

use MikeTAFE\FormValidator;

$data = [
    'username' => 'jo',
    'email' => 'invalid-email',
    'age' => '17',
    'user' => [
        'profile' => ['active' => 'yes']
    ]
];

$rules = [
    'username' => [
        'required',
        'min_length' => [3, 'Username too short.'],
        'max_length' => [20]
    ],
    'email' => [
        'required',
        'email' => 'Invalid email format.'
    ],
    'age' => [
        'int',
        'min' => [18, 'You must be at least 18.']
    ],
    'user.profile.active' => [
        'boolean' => 'Active must be a boolean.'
    ]
];

$validator = new FormValidator();
$errors = $validator->validate($data, $rules);

if (!empty($errors)) {
    print_r($errors);
}
```

---

## Rule reference

| Rule         | Description                                      |
| ------------ | ------------------------------------------------ |
| `required`   | Field must be present and non-empty              |
| `email`      | Must be a valid email address                    |
| `int`        | Must be an integer                               |
| `float`      | Must be a float                                  |
| `boolean`    | Accepts `true`, `false`, `1`, `0`, `'true'`, etc |
| `date`       | Must be in `Y-m-d` format                        |
| `min_length` | Minimum string length                            |
| `max_length` | Maximum string length                            |
| `min`        | Minimum numeric value                            |
| `max`        | Maximum numeric value                            |
| `regex`      | Custom pattern validation using regex            |

---

## Nested field support

Use dot notation to access deeply nested values, like:

```php
'account.profile.first_name' => ['required']
```

---

## Running tests

Requires [PHPUnit](https://phpunit.de/):

```bash
composer install
vendor/bin/phpunit
```

---

## License

MIT License © 2025 Michael Kirkwood-Smith
