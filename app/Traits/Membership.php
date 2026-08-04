<?php

namespace App\Traits;

trait Membership
{
    use UseLog, HasRepositoryFromAdmin;
    public function updateMembershipLevel($user)
    {
        if (!$user) {
            return;
        }

        $membershipLevelRepository = $this->getMembershipLevelRepository();
        $membershipLevels = $membershipLevelRepository->getAll();

        $userPoints = (float) ($user->membership_level_points ?? 0);

        $newLevel = $membershipLevels
            ->sortByDesc(fn($level) => (float) $level->min_points)
            ->first(fn($level) => (float) $level->min_points <= $userPoints);

        if ($newLevel) {
            $user->membership_id = $newLevel->id;
            $user->save();
        }
    }
}
