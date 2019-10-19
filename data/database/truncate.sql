-- Truncates ALL database tables. DO NOT EXECUTE UNLESS YOU WANT TO THROW ALL DATA AWAY!

SET foreign_key_checks = 0;
  TRUNCATE `CachedSearchResult`;
  TRUNCATE `Combination`;
  TRUNCATE `CombinationXItem`;
  TRUNCATE `CombinationXMachine`;
  TRUNCATE `CombinationXMod`;
  TRUNCATE `CombinationXRecipe`;
  TRUNCATE `CombinationXTranslation`;
  TRUNCATE `CraftingCategory`;
  TRUNCATE `Icon`;
  TRUNCATE `IconFile`;
  TRUNCATE `Item`;
  TRUNCATE `Machine`;
  TRUNCATE `MachineXCraftingCategory`;
  TRUNCATE `Mod`;
  TRUNCATE `Recipe`;
  TRUNCATE `RecipeIngredient`;
  TRUNCATE `RecipeProduct`;
  TRUNCATE `Translation`;
SET foreign_key_checks = 1;
