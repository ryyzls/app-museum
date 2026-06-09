<?php

namespace App\Filament\Resources\PostResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('body'),
        ]);
    }
}
