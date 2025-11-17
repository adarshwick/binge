<?php

namespace App\Filament\Resources\SubscriptionPlans\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Toggle;

class SubscriptionPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                TextInput::make('price')->numeric()->required()->suffix('$'),
                CheckboxList::make('features')->options([
                    'Unlimited Swipes' => 'Unlimited Swipes',
                    'See Who Liked Me' => 'See Who Liked Me',
                    'Ad-Free' => 'Ad-Free',
                ])->columns(1),
                Toggle::make('active')->default(true),
            ]);
    }
}
