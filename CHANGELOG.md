# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [0.9.5] - 2017-07-03
### Changed
- `AchievementsStorageInterface::getOwnerCriteriasByType` signature changed to `($owner, string $type, $data = null)`.

## [0.9.4] - 2017-07-02
### Changed
- `AchievementsManager::updateAchievementCriteria` renamed to `updateAchievementCriterias`;
- `array $data = []` argument from `AchievementsManager::updateAchievementCriteria` changed to `$data = null`;
- `Achievement` constructor signature changed from `(array $data = [])` to `(array $data`);
- `AchievementCriteriaChange` now uses `AchievementCriteriaChange::PROGRESS_ACCUMULATE` as default progress type (instead of `PROGRESS_HIGHEST`);

## [0.9.3] - 2017-07-02
### Added
- Optional `array $progressData` argument to `AchievementCriteriaChange` constructor;

### Fixed
- `$progress` argument from `AchievementsStorageInterface::setCriteriaProgressUpdated` now contains actual data passed from `AchievementCriteriaChange` instance.

## [0.9.2] - 2017-07-02
### Added
- Optional `array $data` argument to `AchievementCriteriaProgress` constructor;
- New public `$data` field to `AchievementCriteriaProgress` instances.

## [0.9.1] - 2017-06-30
### Added
- `AchievementCriteria::achievementId` method.

## Changed
- `AchievementsStorageInterface::setAchievementsCompleted` now receive array of `Achievement` objects instead of array of integers.

## [0.9.0] - 2017-06-30
### Initial release.

[Unreleased]: https://github.com/tzurbaev/achievements/compare/0.9.5...HEAD
[0.9.5]: https://github.com/tzurbaev/achievements/compare/0.9.4...0.9.5
[0.9.4]: https://github.com/tzurbaev/achievements/compare/0.9.3...0.9.4
[0.9.3]: https://github.com/tzurbaev/achievements/compare/0.9.2...0.9.3
[0.9.2]: https://github.com/tzurbaev/achievements/compare/0.9.1...0.9.2
[0.9.1]: https://github.com/tzurbaev/achievements/compare/0.9.0...0.9.1
[0.9.0]: https://github.com/tzurbaev/achievements/releases/tag/0.9.0
