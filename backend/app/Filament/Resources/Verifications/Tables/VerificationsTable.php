<?php

namespace App\Filament\Resources\Verifications\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use App\Models\Verification;
use Filament\Tables\Columns\ImageColumn;

class VerificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('User')->searchable(),
                ImageColumn::make('photo_path')->disk('public')->label('Photo'),
                BadgeColumn::make('status')->colors([
                    'primary' => 'pending',
                    'success' => 'approved',
                    'danger' => 'rejected',
                ]),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('approve')->visible(fn(Verification $r) => $r->status === 'pending')->action(fn(Verification $r) => $r->forceFill(['status' => 'approved'])->save()),
                Action::make('reject')->visible(fn(Verification $r) => $r->status === 'pending')->action(fn(Verification $r) => $r->forceFill(['status' => 'rejected'])->save()),
                Action::make('approve_and_verify')->label('Approve & Verify').visible(fn(Verification $r) => $r->status === 'pending').action(function(Verification $r){
                    $r->forceFill(['status' => 'approved'])->save();
                    if ($r->user) {
                        $r->user->forceFill(['verified_at' => now()])->save();
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
