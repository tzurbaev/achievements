<?php

namespace Zurbaev\Achievements\Tests;

use PHPUnit\Framework\TestCase;
use Zurbaev\Achievements\Achievement;
use Zurbaev\Achievements\AchievementCriteria;
use Zurbaev\Achievements\AchievementCriteriaChange;
use Zurbaev\Achievements\AchievementsManager;
use Zurbaev\Achievements\Contracts\CriteriaHandlersManager as CriteriaHandlersManagerContract;
use Zurbaev\Achievements\CriteriaHandlersManager;
use Zurbaev\Achievements\Tests\Fakes\AchievementHandlers\ExampleHandler;
use Zurbaev\Achievements\Tests\Stubs\DummyStorage;

class AchievementsManagerTest extends TestCase
{
    /** @var CriteriaHandlersManagerContract */
    protected $criteriasHandler;

    public function setUp(): void
    {
        parent::setUp();

        $this->criteriasHandler = new CriteriaHandlersManager([
            'example' => ExampleHandler::class,
        ]);
    }

    public function testCriteriasShouldBeUpdated()
    {
        $criterias = [
            new AchievementCriteria([
                'id' => 'test-criteria',
                'type' => 'example',
            ]),
            new AchievementCriteria([
                'id' => 'test-second-criteria',
                'type' => 'example',
            ]),
        ];
        $achievement = new Achievement([
            'id' => 'test-achievement',
            'criterias' => $criterias,
        ]);

        $mockedMethods = [
            'getOwnerCriteriasByType', 'getAchievementsByCriterias',
            'getAchievementForCriteria', 'setCriteriaProgressUpdated',
            'setAchievementsCompleted',
        ];

        $storage = \Mockery::mock(DummyStorage::class.'['.implode(',', $mockedMethods).']');
        $storage->shouldReceive('getOwnerCriteriasByType')->andReturn($criterias);
        $storage->shouldReceive('getAchievementsByCriterias')->andReturn([$achievement]);
        $storage->shouldReceive('getAchievementForCriteria')->andReturn($achievement);
        $storage->shouldReceive('setCriteriaProgressUpdated')->andReturn(true);
        $storage->shouldReceive('setAchievementsCompleted')->andReturn(true);

        $manager = new AchievementsManager($storage, $this->criteriasHandler);

        $result = $manager->updateAchievementCriterias('owner', 'example');
        $this->assertSame(2, $result);
    }

    public function testCriteriaHandlerShouldBeCalled()
    {
        $criteria = new AchievementCriteria(['type' => 'example']);
        $achievement = new Achievement(['id' => 1]);

        $manager = new AchievementsManager(new DummyStorage(), $this->criteriasHandler);

        $result = $manager->getCriteriaChange('owner', $criteria, $achievement);
        $this->assertInstanceOf(AchievementCriteriaChange::class, $result);
        $this->assertSame(10, $result->value);
    }
}
