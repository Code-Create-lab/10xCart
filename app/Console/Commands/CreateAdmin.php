<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

#[Signature('admin:create {email} {--name=Admin}')]
#[Description('Create an admin panel login, or reset the password of an existing one')]
class CreateAdmin extends Command
{
    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $password = (string) $this->secret('Password (at least 10 characters)');

        $validator = Validator::make(compact('email', 'password'), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:10'],
        ]);

        if ($validator->fails()) {
            $this->components->error($validator->errors()->first());

            return self::FAILURE;
        }

        $user = User::updateOrCreate(['email' => $email], [
            'name' => $this->option('name'),
            'password' => $password,
        ]);

        $this->components->info(($user->wasRecentlyCreated ? 'Admin created: ' : 'Password updated: ').$email);

        return self::SUCCESS;
    }
}
