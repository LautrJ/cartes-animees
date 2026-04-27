<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Child;
use App\Models\User;
use App\Notifications\FollowUpEndedNotification;
use App\Notifications\FollowUpStartedNotification;
use Filament\Notifications\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TherapistInvitationController extends Controller
{
    // Régénérer son code d'invitation
    public function regenerate(Request $request): JsonResponse
    {
        $code = Str::random(8);
        $request->user()->update(['invitation_code' => $code]);

        return ApiResponse::success(['invitation_code' => $code]);
    }

    // Parent affilie un enfant à un orthophoniste via code
    public function attach(Request $request, Child $child): JsonResponse
    {
        if ($child->parent_id !== $request->user()->id) {
            return ApiResponse::error('Accès refusé.', 403);
        }

        $validated = $request->validate([
            'invitation_code' => ['required', 'string'],
        ]);

        $therapist = User::where('invitation_code', $validated['invitation_code'])->first();

        if (!$therapist) {
            return ApiResponse::error('Code d\'invitation invalide.', 404);
        }

        $alreadyLinked = $child->therapists()
            ->where('users.id', $therapist->id)
            ->whereNull('child_therapist.ended_at')
            ->exists();

        if ($alreadyLinked) {
            return ApiResponse::error('Cet enfant est déjà suivi par cet orthophoniste.', 409);
        }

        $child->therapists()->attach($therapist->id, [
            'assigned_by' => $request->user()->id,
            'assigned_at' => now(),
            'ended_at'    => null,
        ]);

        Notification::make()
            ->title("{$child->first_name} {$child->last_name} a été rattaché à votre compte.")
            ->success()
            ->sendToDatabase($therapist);

        $child->parent->notify(new FollowUpStartedNotification(
            childFirstName:     $child->first_name,
            therapistFirstName: $therapist->first_name,
            therapistLastName:  $therapist->last_name,
        ));

        return ApiResponse::success(['message' => 'Orthophoniste affilié avec succès.'], 201);
    }

    // Parent retire un orthophoniste du suivi d'un enfant
    public function detach(Request $request, Child $child, User $therapist): JsonResponse
    {
        if ($child->parent_id !== $request->user()->id) {
            return ApiResponse::error('Accès refusé.', 403);
        }

        $child->therapists()->updateExistingPivot($therapist->id, [
            'ended_at' => now(),
        ]);

        $child->parent->notify(new FollowUpEndedNotification(
            childFirstName:     $child->first_name,
            therapistFirstName: $therapist->first_name,
            therapistLastName:  $therapist->last_name,
        ));

        return ApiResponse::success(null, 204);
    }
}
