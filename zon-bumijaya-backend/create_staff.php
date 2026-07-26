<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Lunar\Admin\Models\Staff;
use Illuminate\Support\Facades\Hash;

try {
    $staff = Staff::firstOrCreate(
        ['email' => 'admin@zonbumijaya.com'],
        [
            'firstname' => 'Admin',
            'lastname' => 'User',
            'password' => Hash::make('password123'),
            'admin' => true,
        ]
    );

    // If it already existed but password was different or admin was false
    $staff->password = Hash::make('password123');
    $staff->admin = true;
    $staff->save();

    echo "Staff generated successfully! Email: " . $staff->email . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
