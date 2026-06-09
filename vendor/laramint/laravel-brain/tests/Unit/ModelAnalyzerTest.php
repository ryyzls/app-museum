<?php

use LaraMint\LaravelBrain\Analysis\ModelAnalyzer;

it('detects dispatchesEvents on Order model', function () {
    $analyzer = new ModelAnalyzer;
    $models = $analyzer->analyze(fixture('laravel-project'), ['App\\Models\\Order']);

    expect($models)
        ->toBeArray()
        ->toHaveCount(1)
        ->toHaveKey('App\\Models\\Order');

    $order = array_first($models);

    expect($order)
        ->firedEvents->toBe(["App\Events\OrderPlaced"]);
});

it('detects relationships on User model', function () {
    $analyzer = new ModelAnalyzer;
    $models = $analyzer->analyze(fixture('laravel-project'), ['App\\Models\\User']);

    expect($models)->toHaveKey('App\\Models\\User');
    $user = $models['App\\Models\\User'];

    $types = array_column($user->relationships, 'type');
    expect($types)->toContain('hasMany');
});

it('detects belongsTo relationship on Order model', function () {
    $analyzer = new ModelAnalyzer;
    $models = $analyzer->analyze(fixture('laravel-project'), ['App\\Models\\Order']);

    $order = $models['App\\Models\\Order'];
    $types = array_column($order->relationships, 'type');

    expect($types)->ToBeArray()->toContain('belongsTo');
});
