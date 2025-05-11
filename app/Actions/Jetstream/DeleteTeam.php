<?php

declare(strict_types=1);

namespace App\Actions\Jetstream;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Laravel\Jetstream\Contracts\DeletesTeams;

final class DeleteTeam implements DeletesTeams
{
    public function delete(Team $team): void
    {
        $teamId = (string) $team->id;

        // Delete the photo folders
        foreach (config('app.photo_folders') as $folder) {
            if (Storage::disk($folder)->exists($teamId)) {
                Storage::disk($folder)->deleteDirectory($teamId);
            }
        }

        // Find users with this team as current_team_id
        $affectedUsers = User::where('current_team_id', $team->id)->get();
        \Log::info("Deleting team {$teamId}, affected users: " . json_encode($affectedUsers->pluck('id')->toArray()));

        // Permanently delete the team
        $team->purge();

        // Update current_team_id for affected users
        foreach ($affectedUsers as $user) {
            $otherTeams = $user->allTeams()->where('id', '!=', $team->id);
            $newTeamId = $otherTeams->isNotEmpty() ? $otherTeams->first()->id : null;
            \Log::info("Updating user {$user->id} current_team_id from {$user->current_team_id} to {$newTeamId}", [
                'other_team_ids' => $otherTeams->pluck('id')->toArray(),
            ]);
            $user->current_team_id = $newTeamId;
            $user->save();
        }
    }
}