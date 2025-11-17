<?php

namespace App\Filament\Resources\AppSettings\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;

class AppSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')->required()->unique(ignoreRecord: true),
                TextInput::make('group'),
                RichEditor::make('value')->hidden(fn($get)=>$get('group') !== 'legal'),
                Textarea::make('value')->rows(8)->hidden(fn($get)=>$get('group') === 'legal'),
            ]);
    }
}
