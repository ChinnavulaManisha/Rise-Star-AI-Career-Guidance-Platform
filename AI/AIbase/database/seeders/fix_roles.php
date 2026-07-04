<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = App\Models\User::all();
foreach ($users as $u) {
    if (empty($u->role)) {
        $u->role = "student";
        $u->save();
        echo "Fixed: " . $u->email . "\n";
    } else {
        echo "OK (" . $u->role . "): " . $u->email . "\n";
    }
}
echo "Done.";
