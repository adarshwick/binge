<?php

namespace App\Filament\Resources\ProfilePrompts;

use App\Filament\Resources\ProfilePrompts\Pages\CreateProfilePrompt;
use App\Filament\Resources\ProfilePrompts\Pages\EditProfilePrompt;
use App\Filament\Resources\ProfilePrompts\Pages\ListProfilePrompts;
use App\Filament\Resources\ProfilePrompts\Schemas\ProfilePromptForm;
use App\Filament\Resources\ProfilePrompts\Tables\ProfilePromptsTable;
use App\Models\ProfilePrompt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProfilePromptResource extends Resource
{
    protected static ?string $model = ProfilePrompt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ProfilePromptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProfilePromptsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProfilePrompts::route('/'),
            'create' => CreateProfilePrompt::route('/create'),
            'edit' => EditProfilePrompt::route('/{record}/edit'),
        ];
    }
}
