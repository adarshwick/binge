<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('User')->searchable(),
                TextColumn::make('type')->badge()->sortable(),
                TextColumn::make('gateway')->sortable(),
                TextColumn::make('amount')->money('USD', true)->sortable(),
                BadgeColumn::make('status')->colors([
                    'success' => 'succeeded',
                    'danger' => 'failed',
                    'warning' => 'pending',
                ]),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('gateway')->options([
                    'stripe' => 'Stripe',
                    'paypal' => 'PayPal',
                ]),
                \Filament\Tables\Filters\SelectFilter::make('status')->options([
                    'succeeded' => 'Succeeded',
                    'failed' => 'Failed',
                    'pending' => 'Pending',
                ]),
                \Filament\Tables\Filters\Filter::make('created_at')->form([
                    \Filament\Forms\Components\DatePicker::make('from'),
                    \Filament\Forms\Components\DatePicker::make('to'),
                ])->query(function($query, array $data){
                    return $query
                        ->when($data['from'] ?? null, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                        ->when($data['to'] ?? null, fn($q, $d) => $q->whereDate('created_at', '<=', $d));
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
