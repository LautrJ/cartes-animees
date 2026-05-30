<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\Users\UserResource;
use App\Notifications\WelcomeParentNotification;
use App\Notifications\WelcomeTherapistNotification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $user = $this->record;

        if ($user->role === UserRole::Therapist) {
            $user->update([
                'invitation_code' => Str::upper(Str::random(8)),
            ]);

            $token = Password::createToken($user);
            $resetUrl = config('app.frontend_url').'/reset-password?token='.$token.'&email='.urlencode($user->email);

            $user->notify(new WelcomeTherapistNotification(
                firstName: $user->first_name,
                invitationCode: $user->invitation_code,
                resetUrl: $resetUrl,
            ));
        }

        if ($user->role === UserRole::Parent) {
            $user->notify(new WelcomeParentNotification(
                firstName: $user->first_name,
            ));
        }
    }
}
