<?php

use App\Filament\Widgets\PostStatsWidget;
use LaraMint\LaravelBrain\Analysis\FilamentAnalyzer;
use LaraMint\LaravelBrain\Analysis\FilamentPageDefinition;
use LaraMint\LaravelBrain\Analysis\FilamentPanelDefinition;
use LaraMint\LaravelBrain\Analysis\FilamentRelationManagerDefinition;
use LaraMint\LaravelBrain\Analysis\FilamentResourceDefinition;
use LaraMint\LaravelBrain\Analysis\FilamentWidgetDefinition;

it('returns detected=false when Filament is not installed', function () {
    $result = (new FilamentAnalyzer)->analyze(__DIR__.'/../fixtures/laravel-project');

    expect($result)
        ->detected->toBeFalse()
        ->panels->toBe([])
        ->resources->toBe([]);
});

it('detects Filament when vendor directory exists', function () {
    $result = (new FilamentAnalyzer)->analyze(fixture('filament-project'));

    expect($result['detected'])->toBeTrue();
});

it('extracts the admin panel definition', function () {
    $result = (new FilamentAnalyzer)->analyze(fixture('filament-project'));

    expect($result['panels'])
        ->toHaveCount(1)
        ->andArrayFirstElement()
        ->ToBeInstanceOf(FilamentPanelDefinition::class)
        ->id->toBe('admin')
        ->path->toBe('/admin')
        ->fqcn->toBe('App\\Providers\\Filament\\AdminPanelProvider')
        ->resources->toBe(['App\\Filament\\Resources\\PostResource'])
        ->pages->toBe(['App\\Filament\\Pages\\Settings'])
        ->widgets->toBe(['App\\Filament\\Widgets\\PostStatsWidget']);
});

it('extracts PostResource with correct model FQCN', function () {
    $result = (new FilamentAnalyzer)->analyze(fixture('filament-project'));

    expect($result['resources'])
        ->ToBeArray()
        ->toHaveCount(1)
        ->andArrayFirstElement()
        ->ToBeInstanceOf(FilamentResourceDefinition::class)
        ->fqcn->toBe('App\\Filament\\Resources\\PostResource')
        ->modelFqcn->toBe('App\\Models\\Post')
        ->panelId->toBe('admin');
});

it('extracts resource pages from getPages()', function () {
    $result = (new FilamentAnalyzer)->analyze(fixture('filament-project'));

    expect($result['resources'])
        ->ToBeArray()
        ->toHaveCount(1)
        ->andArrayFirstElement()
        ->ToBeInstanceOf(FilamentResourceDefinition::class)
        ->pages->toBe([
            'index' => "App\Filament\Resources\PostResource\Pages\ListPosts",
            'create' => "App\Filament\Resources\PostResource\Pages\CreatePost",
            'edit' => "App\Filament\Resources\PostResource\Pages\EditPost",
        ]);
});

it('extracts relation managers from getRelations()', function () {
    $result = (new FilamentAnalyzer)->analyze(fixture('filament-project'));

    expect($result['resources'])
        ->ToBeArray()
        ->toHaveCount(1)
        ->andArrayFirstElement()
        ->ToBeInstanceOf(FilamentResourceDefinition::class)
        ->relations->toBe(['App\Filament\Resources\PostResource\RelationManagers\CommentsRelationManager']);
});

it('extracts relation manager definitions', function () {
    $result = (new FilamentAnalyzer)->analyze(fixture('filament-project'));

    expect($result['relationManagers'])
        ->ToBeArray()
        ->toHaveCount(1)
        ->andArrayFirstElement()
        ->ToBeInstanceOf(FilamentRelationManagerDefinition::class)
        ->relationship->ToBe('comments')
        ->fqcn->toBe('App\Filament\Resources\PostResource\RelationManagers\CommentsRelationManager')
        ->parentResourceFqcn->ToBe('App\Filament\Resources\PostResource');
});

it('extracts widget definitions', function () {
    $result = (new FilamentAnalyzer)->analyze(fixture('filament-project'));

    expect($result['widgets'])
        ->ToBeArray()
        ->toHaveCount(1)
        ->andArrayFirstElement()
        ->ToBeInstanceOf(FilamentWidgetDefinition::class)
        ->fqcn->toBe(PostStatsWidget::class)
        ->widgetType->toBe('stats-overview');
});

it('extracts custom page definitions', function () {
    $result = (new FilamentAnalyzer)->analyze(fixture('filament-project'));

    $customPages = array_filter(
        $result['pages'],
        fn ($p) => $p->pageType === 'custom'
    );

    expect(count($customPages))->toBeGreaterThanOrEqual(1);

    $settings = array_values(array_filter($customPages, fn ($p) => str_contains($p->fqcn, 'Settings')));

    expect($settings)
        ->ToBeArray()
        ->toHaveCount(1)
        ->andArrayFirstElement()
        ->ToBeInstanceOf(FilamentPageDefinition::class)
        ->fqcn->toBe('App\\Filament\\Pages\\Settings');
});

it('classifies resource pages by type', function () {
    $result = (new FilamentAnalyzer)->analyze(fixture('filament-project'));

    $resourcePages = array_filter(
        $result['pages'],
        fn ($p) => $p->pageType !== 'custom'
    );

    $types = array_column(array_values($resourcePages), 'pageType');
    expect($types)->toHaveCount(3)->toContain('create', 'edit', 'index');
});

// ── discoverResources / discoverPages tests ───────────────────────────────────

it('detects discoverResources namespace from panel provider', function () {
    $result = (new FilamentAnalyzer)->analyze(fixture('filament-discover-project'));

    expect($result)
        ->ToBeArray()
        ->detected->toBetrue();

    expect($result['panels'])
        ->toBeArray()
        ->andArrayFirstElement()
        ->ToBeInstanceOf(FilamentPanelDefinition::class)
        ->id->toBe('admin')
        ->discoverResourcesFor->toBe(['App\\Filament\\Resources'])
        ->discoverPagesFor->toBe(['App\\Filament\\Pages']);
});

it('links discovered resources to the panel via namespace matching', function () {
    $result = (new FilamentAnalyzer)->analyze(fixture('filament-discover-project'));

    expect($result['panels'])
        ->toBeArray()
        ->andArrayFirstElement()
        ->ToBeInstanceOf(FilamentPanelDefinition::class)
        ->resources->toBe(['App\\Filament\\Resources\\PostResource']);
});

it('links discovered custom pages to the panel via namespace matching', function () {
    $result = (new FilamentAnalyzer)->analyze(fixture('filament-discover-project'));

    expect($result['panels'])
        ->ToBeArray()
        ->andArrayFirstElement()
        ->ToBeInstanceOf(FilamentPanelDefinition::class)
        ->pages->toBe([
            'Pages\\Dashboard',
            'App\\Filament\\Pages\\Settings',
        ]);
});

it('attaches the panel ID to discovered resources', function () {
    $result = (new FilamentAnalyzer)->analyze(fixture('filament-discover-project'));

    expect($result['resources'])
        ->ToBeArray()
        ->andArrayFirstElement()
        ->ToBeInstanceOf(FilamentResourceDefinition::class)
        ->panelId->toBe('admin');
});
