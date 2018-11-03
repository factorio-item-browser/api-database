# Changelog

## Unreleased

### Added

- `RepositoryWithOrphansInterface` to signal that orphans must be cleaned.
- Method `findByNames` to the `ModCombinationRepository`.
- Required parameter `$name` to the constructor of the `ModCombination` entity. 

### Removed

- `EntityType` interface. It has been replaced by the interface from `factorio-item-browser/common`.
- Fluent interface from repository methods.

## 1.0.0 - 2018-08-04

- Initial release of the API database classes.
