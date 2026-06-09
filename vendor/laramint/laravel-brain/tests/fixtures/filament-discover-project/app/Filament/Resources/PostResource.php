<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Resources\Resource;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }
}
