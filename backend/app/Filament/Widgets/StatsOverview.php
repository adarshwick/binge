<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\User;
use App\Models\UserMatch;
use App\Models\Conversation;
use App\Models\Report;
use App\Models\Verification;
use App\Models\Payment;
use App\Models\UserSubscription;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total Users', User::count()),
            Card::make('New Users Today', User::whereDate('created_at', now()->toDateString())->count()),
            Card::make('Matches', UserMatch::count()),
            Card::make('Conversations', Conversation::count()),
            Card::make('Reports (Queued)', Report::where('status', 'queued')->count()),
            Card::make('Verifications (Pending)', Verification::where('status', 'pending')->count()),
            Card::make('Revenue (USD)', number_format((float) Payment::sum('amount'), 2)),
            Card::make('Active Subscriptions', UserSubscription::where('status','active')->count()),
        ];
    }
}