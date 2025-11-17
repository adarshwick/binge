<?php

namespace App\Filament\Resources\Reports\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use App\Models\Report;
use App\Models\User;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reporter.name')->label('Reporter')->searchable(),
                TextColumn::make('reportedUser.name')->label('Reported')->searchable(),
                TextColumn::make('reason')->searchable(),
                BadgeColumn::make('status')->colors([
                    'primary' => 'queued',
                    'warning' => 'warned',
                    'danger' => 'banned',
                ]),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('warn')->visible(fn(Report $r) => $r->status === 'queued')->action(function (Report $r) {
                    $r->forceFill(['status' => 'warned'])->save();
                }),
                Action::make('ban')->visible(fn(Report $r) => $r->status !== 'banned')->action(function (Report $r) {
                    $r->forceFill(['status' => 'banned'])->save();
                    if ($user = User::find($r->reported_user_id)) {
                        $user->forceFill(['banned' => true])->save();
                    }
                }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
