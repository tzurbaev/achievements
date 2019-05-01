<?php

namespace Zurbaev\Achievements\Tests;

use PHPUnit\Framework\TestCase;
use Zurbaev\Achievements\AchievementsManager;
use Zurbaev\Achievements\CriteriaHandlersManager;
use Zurbaev\Achievements\Tests\Fakes\AchievementHandlers\WriteBlogPostsHandler;
use Zurbaev\Achievements\Tests\Stubs\InMemoryStorage;

class CompletionTest extends TestCase
{
    public function testAchievementCanBeCompletedByCompletingAllCriterias()
    {
        $repository = new InMemoryStorage();
        $handlers = new CriteriaHandlersManager([
            'blog.write_posts' => WriteBlogPostsHandler::class,
        ]);
        $manager = new AchievementsManager($repository, $handlers);

        $this->assertSame(0, $repository->points['owner']);

        $result = $manager->updateAchievementCriterias('owner', 'blog.write_posts', [
            'category' => 1,
        ]);

        $this->assertSame(2, $result);
        $this->assertSame(5, $repository->points['owner']);
    }
}
