<?php

namespace App\Filament\Resources\AgentSettlementResource\Pages;

use App\Filament\Resources\AgentSettlementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgentSettlements extends ListRecords
{
    protected static string $resource = AgentSettlementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
