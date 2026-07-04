# Testing Guide

## The Golden Rule
**No Feature Without a Test.**

Every new component must follow this flow before being considered complete:
Implementation -> Unit Test -> Integration Test -> Documentation.

## Test Types
- **Unit Tests (`tests/Unit`)**: Test classes and methods in isolation. Use mock contexts.
- **Integration Tests (`tests/Integration`)**: Run validators against actual fixture data.
- **Golden Tests**: For validators, we use Golden Tests. Provide a fixture, run the validator, generate a `ValidationResult`, and compare it against the expected JSON output.

## Language QA Examples
For testing the `LanguageValidator`, create broken fixtures that ensure our QA bot functions properly:
- `missing_hindi.json`: File omitting the `"hi"` key.
- `missing_english.json`: File omitting the `"en"` key.
- `empty_translation.json`: `"hi": ""`
- `invalid_utf8.json`: A file containing broken string encodings.
- `duplicate_paragraph.json`: Provide two files that share > 95% similarity in their English strings.
- `placeholders.json`: Insert `TODO`, `Lorem ipsum`, or `Coming soon`.
- `html_tags.json`: Insert `<div>` or `<b>` tags to trigger failures.

## Running Tests
Tests are executed via PHPUnit. Run:
`vendor/bin/phpunit`
