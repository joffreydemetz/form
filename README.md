# JDZ Form

A framework-agnostic PHP library for building, filtering and validating forms.

## Features

- 🧱 **Rich field set**: text, email, url, tel, password, search, number, date/datetime/time/month/week/year, color, file, hidden, checkbox, radio, select, textarea
- 🧹 **Filters**: normalize input before validation (string, int, decimal, bool, array, email, url, tel, date, time, color, or a custom callback)
- ✅ **Rules**: declarative validation with typed error handling
- 🗂️ **Structure**: group fields into fieldsets and form rows (including boolean, checkboxes, input-group and hidden rows)
- 🔒 **CSRF & captcha**: opt-in per form
- 🔗 **Fluent API**: chainable configuration, framework-agnostic rendering via `toData()`
- ✅ **Well tested**: 285 tests

## Installation

```bash
composer require jdz/form
```

## Requirements

- PHP 8.2 or higher

## Quick start

```php
use JDZ\Form\Form;
use JDZ\Form\FormData;

$form = new Form('contact');

$fieldset = $form->makeFormFieldset('main');
// add fields / rows to the fieldset, each with its filters and rules

// bind submitted values and run the lifecycle
$form->init(new FormData($_POST));

if ($form->submit()) {
    // $form->filter();  normalize
    // $form->validate(); apply rules
    if (!$form->getErrors()) {
        // persist $form->getData()
    }
}
```

Forms expose their state through `toData()`, so rendering is left entirely to the
consuming application (Twig, plain PHP, an admin UI layer, etc.).

## Testing

```bash
composer test
```

## License

MIT — see [LICENSE](LICENSE).
