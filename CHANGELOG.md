# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [0.9.2]
### Added
- Optional `array $data` argument to `AchievementCriteriaProgress` constructor;
- New public `$data` field to `AchievementCriteriaProgress` instances.

## [0.9.1]
### Added
- `AchievementCriteria::achievementId` method.

## Changed
- `AchievementsStorageInterface::setAchievementsCompleted` now receive array of `Achievement` objects instead of array of integers.

## [0.9.0]
### Initial release.

[Unreleased]: https://github.com/tzurbaev/achievements
[0.9.2]: https://github.com/tzurbaev/achievements/compare/0.9.1...0.9.2
[0.9.1]: https://github.com/tzurbaev/achievements/compare/0.9.0...0.9.1
[0.9.0]: https://github.com/tzurbaev/achievements/releases/tag/0.9.0
