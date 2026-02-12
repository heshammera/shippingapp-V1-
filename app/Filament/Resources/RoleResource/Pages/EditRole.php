<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load current permissions
        $data['permissions'] = $this->record->permissions->pluck('name')->toArray();
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract permissions from data
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);
        
        // Store for later sync
        $this->permissions = $permissions;
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Sync permissions after role update
        if (isset($this->permissions)) {
            $this->record->syncPermissions($this->permissions);
        }
    }

    protected $permissions = [];
}
