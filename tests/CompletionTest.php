<?php

namespace Zurbaev\Achievements\Tests;

use PHPUnit\Framework\TestCase;
use Zurbaev\Achievements\Achievement;
use Zurbaev\Achievements\AchievementCriteria;
use Zurbaev\Achievements\AchievementCriteriaChange;
use Zurbaev\Achievements\AchievementsManager;
use Zurbaev\Achievements\Tests\Stubs\InMemoryStorage;

class CompletionTest extends TestCase
{
    public function testAchievementCanBeCompletedByCompletingAllCriterias()
    {
        $repository = new InMemoryStorage();
        $manager = new AchievementsManager($repository);

        $this->assertSame(0, $repository->points['owner']);

        AchievementsManager::registerHandler('blog.write_posts', function ($owner, AchievementCriteria $cr, Achievement $ach, array $data) {
            if (!$cr->hasRequirement('category')) {
                return new AchievementCriteriaChange(1, AchievementCriteriaChange::PROGRESS_ACCUMULATE);
            }

            if (!isset($data['category'])) {
                return null;
            }

            if (intval($cr->requirement('category')) !== intval($data['category'] ?? 0)) {
                return null;
            }

            return new AchievementCriteriaChange(1, AchievementCriteriaChange::PROGRESS_ACCUMULATE);
        });

        $result = $manager->updateAchievementCriterias('owner', 'blog.write_posts', [
            'category' => 1,
        ]);

        $this->assertSame(2, $result);
        $this->assertSame(5, $repository->points['owner']);
    }
}
