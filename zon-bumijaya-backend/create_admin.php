<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    $user = User::firstOrCreate(
        ['email' => 'admin@zonbumijaya.com'],
        [
            'name' => 'Admin',
            'password' => Hash::make('password123')
        ]
    );

    // Some systems (like Filament with Spatie roles) might need a specific role or admin flag, 
    // but default Filament just checks if the user can access the panel.
    // Usually Filament uses a `canAccessPanel` method on the User model.
    // Let's just update the password in case it already existed with a different password.
    $user->password = Hash::make('password123');
    $user->save();

    echo "User generated successfully! Email: " . $user->email . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
