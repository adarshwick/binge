<?php

namespace App\Filament\Resources\AppSettings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\AppSetting;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\TextInput;

class AppSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->searchable()->sortable(),
                TextColumn::make('group')->searchable()->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->options(fn() => AppSetting::query()->whereNotNull('group')->distinct()->pluck('group','group')->toArray()),
                Filter::make('key')
                    ->form([
                        TextInput::make('key_contains')->label('Key contains'),
                    ])
                    ->query(function ($query, array $data) {
                        if (!empty($data['key_contains'])) {
                            $query->where('key', 'like', '%'.$data['key_contains'].'%');
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
