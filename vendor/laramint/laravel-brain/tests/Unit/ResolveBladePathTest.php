<?php

declare(strict_types=1);

use LaraMint\LaravelBrain\Graph\GraphBuilder;

function deleteTree(string $dir): void
{
    if (! is_dir($dir)) {
        return;
    }
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $fileinfo) {
        $path = $fileinfo->getPathname();
        if ($fileinfo->isDir()) {
            @rmdir($path);
        } else {
            @unlink($path);
        }
    }
    @rmdir($dir);
}

it('resolves module-style namespaced views under Modules/{studly}/resources/views', function () {
    $tmp = sys_get_temp_dir().'/laravel-brain-blade-'.uniqid('', true);
    mkdir($tmp.'/Modules/Blog/resources/views/posts', 0777, true);
    $expected = $tmp.'/Modules/Blog/resources/views/posts/index.blade.php';
    file_put_contents($expected, '<div>ok</div>');

    try {
        $builder = new GraphBuilder;
        $rootProp = new ReflectionProperty(GraphBuilder::class, 'projectRoot');

        if (\PHP_VERSION_ID < 80100) {
            $rootProp->setAccessible(true);
        }

        $rootProp->setValue($builder, $tmp);

        $method = new ReflectionMethod(GraphBuilder::class, 'resolveBladePath');

        if (\PHP_VERSION_ID < 80100) {
            $method->setAccessible(true);
        }

        expect($method->invoke($builder, 'blog::posts.index'))->toBe($expected);
    } finally {
        deleteTree($tmp);
    }
});

it('resolves namespaced views under resources/views/vendor/{hint}', function () {
    $tmp = sys_get_temp_dir().'/laravel-brain-blade-'.uniqid('', true);
    mkdir($tmp.'/resources/views/vendor/acme', 0777, true);
    $expected = $tmp.'/resources/views/vendor/acme/widget.blade.php';
    file_put_contents($expected, '<div>v</div>');

    try {
        $builder = new GraphBuilder;
        $rootProp = new ReflectionProperty(GraphBuilder::class, 'projectRoot');

        if (\PHP_VERSION_ID < 80100) {
            $rootProp->setAccessible(true);
        }

        $rootProp->setValue($builder, $tmp);

        $method = new ReflectionMethod(GraphBuilder::class, 'resolveBladePath');

        if (\PHP_VERSION_ID < 80100) {
            $method->setAccessible(true);
        }

        expect($method->invoke($builder, 'acme::widget'))->toBe($expected);
    } finally {
        deleteTree($tmp);
    }
});

it('falls back to scanning Modules/*/resources/views when studly folder name differs', function () {
    $tmp = sys_get_temp_dir().'/laravel-brain-blade-'.uniqid('', true);
    mkdir($tmp.'/Modules/CustomBlog/resources/views', 0777, true);
    $expected = $tmp.'/Modules/CustomBlog/resources/views/home.blade.php';
    file_put_contents($expected, '<div>h</div>');

    try {
        $builder = new GraphBuilder;
        $rootProp = new ReflectionProperty(GraphBuilder::class, 'projectRoot');

        if (\PHP_VERSION_ID < 80100) {
            $rootProp->setAccessible(true);
        }

        $rootProp->setValue($builder, $tmp);

        $method = new ReflectionMethod(GraphBuilder::class, 'resolveBladePath');
        if (\PHP_VERSION_ID < 80100) {
            $method->setAccessible(true);
        }

        expect($method->invoke($builder, 'blog::home'))->toBe($expected);
    } finally {
        deleteTree($tmp);
    }
});
