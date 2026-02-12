<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract permissions from data
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Sync permissions after role creation
        $permissions = $this->data['permissions'] ?? [];
        $this->record->syncPermissions($permissions);
    }
}
