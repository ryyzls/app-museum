<?php

use LaraMint\LaravelBrain\Analysis\ValidationRulesExtractor;

it('detects a concrete rules() method', function () {
    $extractor = new ValidationRulesExtractor;
    expect($extractor->hasNonAbstractRulesMethod(fixture('/laravel-project/app/Http/Requests/ProfileStoreRequest.php')))->toBeTrue();
});

it('extracts validation rows from rules() return arrays', function () {
    $extractor = new ValidationRulesExtractor;
    $rows = $extractor->extractFromFile(fixture('/laravel-project/app/Http/Requests/ProfileStoreRequest.php'));

    expect($rows)->toBeNonEmptyArray();

    $fields = array_column($rows, 'field');
    expect($fields)->toBe(["'name'", "'email'"]);

    $rulesText = array_column($rows, 'rules');
    expect($rulesText)->toBe([
        "'required', 'string', 'max:255'",
        "'required|email'",
    ]);
});
