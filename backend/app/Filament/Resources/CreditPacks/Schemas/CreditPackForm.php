<?php

namespace App\Filament\Resources\CreditPacks\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class CreditPackForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                TextInput::make('credits')->numeric()->required(),
                TextInput::make('price')->numeric()->required()->suffix('$'),
                Toggle::make('active')->default(true),
            ]);
    }
}
