<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Hash password
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        // Calculate expires_at
        if (isset($data['lifetime']) && $data['lifetime']) {
            $data['expires_at'] = null; // مدى الحياة
        } elseif (isset($data['expires_days'])) {
            $data['expires_at'] = now()->addDays($data['expires_days']);
        }
        
        // Remove helper fields
        unset($data['expires_days'], $data['lifetime'], $data['permissions'], $data['roles']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Assign roles
        if (isset($this->data['roles'])) {
            $this->record->syncRoles($this->data['roles']);
        }
        
        // Assign direct permissions
        if (isset($this->data['permissions'])) {
            $this->record->syncPermissions($this->data['permissions']);
        }
    }
}
