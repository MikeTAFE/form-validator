<?php

// Load dependencies
require __DIR__ . '/../vendor/autoload.php';

use MikeTAFE\FormValidator;

$errors = [];
$success = false;

// Simulate submitted form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = $_POST;

  // Define validation rules using all rule types
  $rules = [
    'name' => [
      'required',
      'min_length' => [3, 'Name must be at least 3 characters.'],
      'max_length' => [50]
    ],
    'email' => [
      'required',
      'email' => 'Please enter a valid email address.'
    ],
    'age' => [
      'required',
      'int' => 'Age must be an integer.',
      'min' => [18, 'You must be at least 18 years old.'],
      'max' => [99]
    ],
    'salary' => [
      'float' => 'Salary must be a decimal number.'
    ],
    'terms' => [
      'boolean' => 'You must accept the terms.'
    ],
    'birthdate' => [
      'date' => 'Birthdate must be in YYYY-MM-DD format.'
    ],
    'username' => [
      'regex' => ['/^@[a-zA-Z0-9_]{3,20}$/', 'Username must start with "@" and be 3-20 characters.']
    ],
    'profile.bio' => [
      'max_length' => [150, 'Bio must be 150 characters or fewer.']
    ]
  ];

  // Run validation
  $validator = new FormValidator();
  $errors = $validator->validate($data, $rules);
  $success = empty($errors);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FormValidator demo website</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="container">
    <h1>FormValidator demo website</h1>

    <p>This website demonstrates the usage of every validation rule for FormValidator with custom messages. This should help you to learn how to use this simple PHP validation library with your own forms.</p>

    <?php if ($success): ?>
      <div class="success">Form submitted successfully! 🎉</div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <!-- Name (required, min_length, max_length) -->
      <label>Name *</label>
      <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
      <span class="error"><?= $errors['name'] ?? '' ?></span>

      <!-- Email (required, email) -->
      <label>Email *</label>
      <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      <span class="error"><?= $errors['email'] ?? '' ?></span>

      <!-- Age (required, int, min, max) -->
      <label>Age *</label>
      <input type="number" name="age" value="<?= htmlspecialchars($_POST['age'] ?? '') ?>">
      <span class="error"><?= $errors['age'] ?? '' ?></span>

      <!-- Salary (float) -->
      <label>Expected salary</label>
      <input type="text" name="salary" value="<?= htmlspecialchars($_POST['salary'] ?? '') ?>">
      <span class="error"><?= $errors['salary'] ?? '' ?></span>

      <!-- Terms (boolean) -->
      <label>
        <input type="checkbox" name="terms" value="1" <?= isset($_POST['terms']) ? 'checked' : '' ?>>
        I accept the terms and conditions
      </label>
      <span class="error"><?= $errors['terms'] ?? '' ?></span>

      <!-- Birthdate (date) -->
      <label>Birthdate (yyyy-mm-dd)</label>
      <input type="text" name="birthdate" value="<?= htmlspecialchars($_POST['birthdate'] ?? '') ?>">
      <span class="error"><?= $errors['birthdate'] ?? '' ?></span>

      <!-- Username (regex) -->
      <label>Username (starts with @)</label>
      <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      <span class="error"><?= $errors['username'] ?? '' ?></span>

      <!-- Bio (max_length, nested field) -->
      <label>Short bio</label>
      <textarea name="profile[bio]"><?= htmlspecialchars($_POST['profile']['bio'] ?? '') ?></textarea>
      <span class="error"><?= $errors['profile.bio'] ?? '' ?></span>

      <button type="submit">Submit</button>
    </form>
  </div>
</body>
</html>