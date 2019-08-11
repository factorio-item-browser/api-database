# Changelog

## 2.1.1 - 2019-08-11

### Fixed

- Some unescaped column names leading to SQL errors.

## 2.1.0 - 2019-07-15

### Added

- Property `size` to the `IconFile` entity. 

### Changed

- Value for unlimited item slots in machine from -1 to 255 to use the same value as in-game.
- Usage of `ReflectionFactory`to `AutoWireFactory`

## 2.0.0 - 2019-04-07

### Added

- `RepositoryWithOrphansInterface` to signal that orphans must be cleaned.
- Method `findByNames` to the `ModCombinationRepository`.
- Required parameter `$name` to the constructor of the `ModCombination` entity.
- Constant interface for the types of the `Translation` entity. 

### Changed

- Using `EntityManagerInterface` in the container instead of the `EntityManager` itself.
- Repositories no longer inherit from the `EntityRepository` and are registered directly to the container.
- Type hints from `DateTime` to `DateTimeInterface`.

### Fixed

- Error in entity relation between `CraftingCategory` and `Recipe`.

### Removed

- `EntityType` interface. It is replaced by the interface from `factorio-item-browser/common`.
- Fluent interface from repository methods.

## 1.0.0 - 2018-08-04

- Initial release of the API database classes.
