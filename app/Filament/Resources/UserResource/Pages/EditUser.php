<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

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
        // Load roles and permissions
        $data['roles'] = $this->record->roles->pluck('id')->toArray();
        $data['permissions'] = $this->record->getDirectPermissions()->pluck('name')->toArray();
        
        // Set lifetime flag
        $data['lifetime'] = is_null($this->record->expires_at);
        
        // Calculate remaining days
        if (!$data['lifetime'] && $this->record->expires_at) {
            $data['expires_days'] = now()->diffInDays($this->record->expires_at);
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hash password if changed
        if (isset($data['password']) && filled($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        // Update expires_at
        if (isset($data['lifetime']) && $data['lifetime']) {
            $data['expires_at'] = null;
        } elseif (isset($data['expires_days'])) {
            $data['expires_at'] = now()->addDays($data['expires_days']);
        }
        
        // Store roles and permissions for later sync
        $this->tmpRoles = $data['roles'] ?? [];
        $this->tmpPermissions = $data['permissions'] ?? [];
        
        // Remove helper fields
        unset($data['expires_days'], $data['lifetime'], $data['permissions'], $data['roles']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Sync roles
        if (isset($this->tmpRoles)) {
            $this->record->syncRoles($this->tmpRoles);
        }
        
        // Sync permissions
        if (isset($this->tmpPermissions)) {
            $this->record->syncPermissions($this->tmpPermissions);
        }
    }

    protected $tmpRoles = [];
    protected $tmpPermissions = [];
}
