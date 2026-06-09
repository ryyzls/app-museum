<?php

use PHPUnit\Framework\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

// Unit tests use plain PHPUnit\Framework\TestCase (no Laravel)
pest()->extend(TestCase::class)->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeNonEmptyArray', function () {
    return $this->toBeArray()->not->toBeEmpty();
});

expect()->extend('andArrayFirstElement', function () {
    expect($this->value)->toBeNonEmptyArray();

    $this->value = array_first($this->value);

    return $this;
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

if (! function_exists('fixture')) {
    /**
     * Returns fixture file or directory path
     *
     * @throws RuntimeException if the file or directory does not exist.
     */
    function fixture(string $fixture): string
    {
        $path = __DIR__.str_replace('/', DIRECTORY_SEPARATOR, '//fixtures/'.$fixture);

        if (! file_exists($path)) {
            throw new RuntimeException("Directory or file missing {$path}]");
        }

        return $path;
    }
}
