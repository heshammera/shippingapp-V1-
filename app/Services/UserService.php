<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserService
{
    /**
     * Create a new user with role and specialized logic.
     */
    public function createUser(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                $expiresAt = $this->calculateExpiration(
                    $data['expires_days'] ?? null,
                    isset($data['expires_lifetime'])
                );

                // Prepare base data
                $userData = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'phone' => $data['phone'] ?? null,
                    'address' => $data['address'] ?? null,
                    'is_active' => isset($data['is_active']) && $data['is_active'],
                    'shipping_company_id' => $data['shipping_company_id'] ?? null,
                    'expires_at' => $expiresAt,
                    // Legacy field support if needed
                    'role' => $data['role'], 
                ];

                // Create User
                $user = User::create($userData);

                // Assign Spatie Role
                $user->assignRole($data['role']);

                \Illuminate\Support\Facades\Log::info("User created successfully: {$user->id} ({$user->email}) with role {$data['role']}");

                return $user;

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error creating user: " . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Update an existing user.
     */
    public function updateUser(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            try {
                // Calculate new expiration only if provided
                $expiresAt = $user->expires_at; 
                if (isset($data['expires_lifetime']) || !empty($data['expires_days'])) {
                    $expiresAt = $this->calculateExpiration(
                        $data['expires_days'] ?? null,
                        isset($data['expires_lifetime'])
                    );
                }

                // Prepare update data
                $updateData = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'address' => $data['address'] ?? null,
                    'is_active' => isset($data['is_active']) && $data['is_active'],
                    'shipping_company_id' => $data['shipping_company_id'] ?? null,
                ];

                // Only update password if provided
                if (!empty($data['password'])) {
                    $updateData['password'] = Hash::make($data['password']);
                }

                // Only update expiration if changed
                if (isset($data['expires_lifetime']) || !empty($data['expires_days'])) {
                    $updateData['expires_at'] = $expiresAt;
                }

                // Legacy role field update
                if (!empty($data['role'])) {
                    $updateData['role'] = $data['role'];
                }

                $user->update($updateData);

                // Sync Spatie Role
                if (!empty($data['role'])) {
                    $user->syncRoles([$data['role']]);
                }

                \Illuminate\Support\Facades\Log::info("User updated successfully: {$user->id} ({$user->email})");

                return $user;

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error updating user {$user->id}: " . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Calculate expiration date based on input.
     */
    protected function calculateExpiration($days, $isLifetime)
    {
        if ($isLifetime) {
            return now()->addYears(100);
        }

        if ($days && $days > 0) {
            return now()->addDays($days);
        }

        return null; // Or existing logic as needed
    }
}
