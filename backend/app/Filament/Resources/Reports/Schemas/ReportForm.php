<?php

namespace App\Filament\Resources\Reports\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('reporter_id')->relationship('reporter','name')->label('Reporter')->required(),
                Select::make('reported_user_id')->relationship('reportedUser','name')->label('Reported User')->required(),
                TextInput::make('reason')->required(),
                Select::make('status')->options([
                    'queued' => 'Queued',
                    'warned' => 'Warned',
                    'banned' => 'Banned',
                ])->required(),
                Textarea::make('notes'),
                FileUpload::make('attachment_path')->disk('public')->directory('reports')->label('Attachment'),
            ]);
    }
}
