# Changelog

## 3.3.0 - 2020-07-14

### Added

- Method `findByNames()` to `CraftingCategoryRepository` to search for crafting categories by their names.
- Method `clearCombination()` to `IconRepository` clearing all icons assigned to a combination.

### Changed

- Method `persistTranslationsToCombination()` of `TranslationRepository` no longer clears the cross-table of the 
  combination. Use method `clearCrossTable()` for that, which is now public. 

### Fixed

- Inserting too many translations at once leading to an SQL error because of a too large query.

## 3.2.0 - 2020-06-03

### Added

- Method `findByLastUsageTime()` to `CombinationRepository` allowing searching for recently used combinations.

### Changed

- Charset of all tables to `utf8mb4` to actually be UTF-8.
- All identifying columns' collation to `utf8mb4_bin` to make them case-sensitive, as they are in the game.
- `findByKeywords()` of `TranslationRepository` no longer uses `type` and `name` columns to match the keywords.

## 3.1.0 - 2020-05-02

### Added

- Method `findAll()` to the `ItemRepository` returning all available items.
- Method `findAllData()` to the `RecipeRepository` returning the data of all available recipes.

### Changed 

- Dependency `dasprid/container-interop-doctrine` to `roave/psr-container-doctrine`.

## 3.0.0 - 2020-04-15

### Changed

- Database structure to be based on combinations, not on mods. Requesting data is now based on exactly one combination
  and no longer a set of several combinations.
- `TranslationRepository->findDataByKeywords()` now will only search for the specified locale and "en", ignoring all
  other locales, to get more relevant search results.

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
