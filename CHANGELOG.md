# Changelog

## Unreleased

### Added

- `RepositoryWithOrphansInterface` to signal that orphans must be cleaned.
- Method `findByNames` to the `ModCombinationRepository`.
- Required parameter `$name` to the constructor of the `ModCombination` entity.
- Constant interface for the types of the `Translation` entity. 

### Fixed

- Error in entity relation between `CraftingCategory` and `Recipe`.

### Removed

- `EntityType` interface. It has been replaced by the interface from `factorio-item-browser/common`.
- Fluent interface from repository methods.

## 1.0.0 - 2018-08-04

- Initial release of the API database classes.
