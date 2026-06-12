<?php

namespace App\Traits;

trait Membership
{
    use UseLog, HasRepositoryFromAdmin;
    public function updateMembershipLevel($user)
    {
        $membershipLevelRepository = $this->getMembershipLevelRepository();
        $membershipLevels = $membershipLevelRepository->getAll();

        $newLevel = $membershipLevels->where('min_points', '<=', $user->membership_level_points)
            ->sortByDesc('min_points')
            ->first();

        if ($newLevel) {
            $user->membership_id = $newLevel->id;
        }
        $user->save();
    }
}
