<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

$email = 'admin2@shippingapp.com';
$password = 'password123';

echo "Attempting to create admin user...\n";

// Check if user exists
if (User::where('email', $email)->exists()) {
    echo "User with email {$email} already exists.\n";
    exit(1);
}

try {
    $user = new User();
    $user->name = 'Admin User 2';
    $user->email = $email;
    $user->password = Hash::make($password);
    $user->is_active = true;
    $user->phone = '01000000000'; // Dummy phone
    // $user->role = 'admin'; // Column does not exist
    // $user->role_id = 4;    // Column does not exist
    $user->save();

    // Assign Spatie Role if it exists
    if (Role::where('name', 'admin')->exists()) {
        $user->assignRole('admin');
        echo "Spatie Role 'admin' assigned.\n";
    } else {
        echo "Warning: Spatie Role 'admin' not found.\n";
    }

    echo "User created successfully!\n";
    echo "Email: {$email}\n";
    echo "Password: {$password}\n";

} catch (\Exception $e) {
    echo "Error creating user:Str " . $e->getMessage() . "\n";
    exit(1);
}
