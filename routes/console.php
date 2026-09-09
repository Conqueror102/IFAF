<?php

use App\Models\Booking;
use App\Models\FestivalSetting;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('ifaf:admin {email} {--name=IFAF Admin}', function () {
    $email = $this->argument('email');
    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->error('Enter a valid email.');

        return 1;
    }
    $password = $this->secret('Choose a password (at least 12 characters)');
    if (strlen($password ?? '') < 12) {
        $this->error('Password must have at least 12 characters.');

        return 1;
    }
    $user = User::firstOrNew(['email' => $email]);
    $name = $this->option('name');
    if (! is_string($name) || trim($name) === '') {
        $this->error('Enter a valid administrator name.');

        return 1;
    }
    $user->name = $name;
    $user->password = $password;
    $user->is_admin = true;
    $user->save();
    $this->info('Administrator saved. Sign in at /admin/login.');
})->purpose('Create or update an IFAF administrator securely');

Artisan::command('ifaf:demo-admin', function () {
    if (! app()->environment('local') || ! FestivalSetting::findOrFail(1)->demo) {
        $this->error('Demo admin setup is only available locally in demo mode.');

        return 1;
    }
    if (User::where('email', 'demo@ifaf.test')->exists()) {
        $this->info('Demo administrator already exists. Use ifaf:admin to reset its password if needed.');

        return 0;
    }
    $password = Str::random(24);
    $user = new User;
    $user->name = 'IFAF Demo Admin';
    $user->email = 'demo@ifaf.test';
    $user->password = $password;
    $user->is_admin = true;
    $user->save();
    Storage::disk('local')->put('demo-admin-access.txt', "Local demo admin\nURL: http://localhost:8000/admin/login\nEmail: demo@ifaf.test\nPassword: ".$password."\n\nLocal testing only. Replace or remove this account before launch.\n");
    $this->info('Demo access saved to storage/app/private/demo-admin-access.txt.');
})->purpose('Create a local-only demo admin with a randomly generated password');

Artisan::command('ifaf:expire', function () {
    $count = Booking::whereIn('status', ['awaiting_payment', 'needs_information'])->where('expires_at', '<=', now())->update(['status' => 'expired']);
    $this->info($count.' reservations expired.');
})->purpose('Release unpaid expired reservations')->everyMinute();
