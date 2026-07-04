<?php

namespace App\Traits;

trait HasExperience
{
    /**
     * Add XP to the user and level up if threshold is met.
     *
     * @param int $amount
     * @return bool Returns true if the user leveled up.
     */
    public function addXp(int $amount): bool
    {
        $this->xp = (int) ($this->xp ?? 0) + $amount;
        $this->level = (int) ($this->level ?? 1);
        $leveledUp = false;

        $nextLevelXp = $this->getXpForNextLevel();

        while ($this->xp >= $nextLevelXp) {
            $this->level++;
            $leveledUp = true;
            $nextLevelXp = $this->getXpForNextLevel();
            
            // Automatic Level Badges
            if ($this->level == 5) $this->awardBadge('level_5', 'Dedicated Explorer', '🌟');
            if ($this->level == 10) $this->awardBadge('level_10', 'Master of Destiny', '👑');
        }

        $this->save();

        return $leveledUp;
    }

    /**
     * Check if the user has a specific badge.
     */
    public function hasBadge(string $key): bool
    {
        $badges = $this->earned_badges ?? [];
        foreach ($badges as $badge) {
            if (isset($badge['key']) && $badge['key'] === $key) {
                return true;
            }
        }
        return false;
    }

    /**
     * Award a badge to the user.
     */
    public function awardBadge(string $key, string $name, string $icon): bool
    {
        if ($this->hasBadge($key)) return false;

        $badges = $this->earned_badges ?? [];
        $badges[] = [
            'key' => $key,
            'name' => $name,
            'icon' => $icon,
            'earned_at' => now()->toDateTimeString()
        ];
        
        $this->earned_badges = $badges;
        $this->save();
        
        // Flash to session to show a toast
        session()->push('gamification_badges', ['name' => $name, 'icon' => $icon]);
        
        return true;
    }

    /**
     * Get the total XP requirement to reach a specific level.
     */
    public function getXpRequirementForLevel(int $level): int
    {
        if ($level <= 1) return 0;
        // Level 1 = 0
        // Level 2 = 100
        // Level 3 = 300
        // Level 4 = 600
        return 100 * ($level - 1) + 50 * ($level - 2) * ($level - 1);
    }

    /**
     * Get the total XP required for the next level.
     */
    public function getXpForNextLevel(): int
    {
        $level = (int) ($this->level ?? 1);
        return $this->getXpRequirementForLevel($level + 1);
    }

    /**
     * Get the progress percentage towards the next level (0-100).
     */
    public function getLevelProgressPercentage(): int
    {
        $level = (int) ($this->level ?? 1);
        $xp = (int) ($this->xp ?? 0);

        $currentLevelXp = $this->getXpRequirementForLevel($level);
        $nextLevelXp = $this->getXpForNextLevel();
        
        $xpEarnedThisLevel = $xp - $currentLevelXp;
        $xpNeededThisLevel = $nextLevelXp - $currentLevelXp;
        
        if ($xpNeededThisLevel <= 0) return 0;

        $progress = (int) round(($xpEarnedThisLevel / $xpNeededThisLevel) * 100);

        // Clamp to 0-100 to prevent display overflow
        return max(0, min(100, $progress));
    }
}
