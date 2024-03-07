<?php
namespace PrestigeSociety\Management;
use JetBrains\PhpStorm\Pure;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ListTag;
use PrestigeSociety\Core\Utils\RandomUtils;
class StaticManagement{
        const ABILITY_TAG = "__ability__";
        const ABILITY_DURATION_TAG = "__ability_duration__";

        const MAX_SLOTS_TAG = "__max_slots__";
        const DEFAULT_MAX_SLOTS = 3;

        const ABILITY_MONEY_BOOST = 0;
        const ABILITY_DOUBLE_BOSS_REWARD = 1;

        /**
         * @param Item $item
         *
         * @return int|null
         */
        public static function getItemAbility(Item $item): ?int{
                return $item->getNamedTag()->getInt(self::ABILITY_TAG, -1) !== -1 ? $item->getNamedTag()->getInt(self::ABILITY_TAG) : null;
        }

        /**
         * @param Item $item
         *
         * @return int
         */
        public static function getItemAbilityDuration(Item $item): int{
                return $item->getNamedTag()->getInt(self::ABILITY_DURATION_TAG, false) != false ? $item->getNamedTag()->getInt(self::ABILITY_DURATION_TAG) : 0;
        }

        /**
         * @param Item $item
         * @param int  $id
         *
         * @return Item
         */
        public static function setItemAbility(Item $item, int $id): Item{
                $item->getNamedTag()->setInt(self::ABILITY_TAG, $id);
                return $item;
        }

        /**
         * @param Item $item
         * @param int  $duration
         *
         * @return Item
         */
        public static function setItemAbilityDuration(Item $item, int $duration): Item{
                $item->getNamedTag()->setInt(self::ABILITY_DURATION_TAG, $duration);
                return $item;
        }

        /**
         * @param Item $item
         *
         * @return Item
         */
        public static function removeItemAbility(Item $item): Item{
                $item->getNamedTag()->removeTag(self::ABILITY_TAG);
                return $item;
        }

        /**
         * @param Item $item
         * @return Item
         */
        public static function removeItemAbilityDuration(Item $item): Item{
                $item->getNamedTag()->removeTag(self::ABILITY_DURATION_TAG);
                return $item;
        }

        /**
         * @param Item $item
         *
         * @return int
         */
        public static function getItemMaxSlots(Item $item): int{
                $tag = $item->getNamedTag()->getInt(self::MAX_SLOTS_TAG, false);
                if(!$tag) self::setItemMaxSlots($item, self::DEFAULT_MAX_SLOTS);
                return $item->getNamedTag()->getInt(self::MAX_SLOTS_TAG);
        }

        /**
         * @param Item $item
         * @param int  $slots
         *
         * @return Item
         */
        public static function setItemMaxSlots(Item $item, int $slots): Item{
                $item->getNamedTag()->setInt(self::MAX_SLOTS_TAG, $slots);
                return $item;
        }

        /**
         * @param Item $item
         * @param int  $slots
         *
         * @return Item
         */
        public static function addItemMaxSlots(Item $item, int $slots = 1): Item{
                $item->getNamedTag()->setInt(self::MAX_SLOTS_TAG, (self::getItemMaxSlots($item) ?? 0) + $slots);
                return $item;
        }

        /**
         * @param int $id
         *
         * @return null|string
         */
        public static function abilityIdToName(int $id): ?string{
                $abilities = [
                    self::ABILITY_MONEY_BOOST => "Money Boost",
                    self::ABILITY_DOUBLE_BOSS_REWARD => "Double Boss Reward"
                ];
                return $abilities[$id] ?? null;
        }

        /**
         * @param Item $item
         *
         * @return array
         */
        #[Pure] public static function getAvailableAbilities(Item $item): array{
                $toolType = $item->getBlockToolType();
                return $toolType <= 0 ? [] : ($toolType > 1 ? [
                    self::ABILITY_MONEY_BOOST,
                ] : [
                    self::ABILITY_DOUBLE_BOSS_REWARD
                ]);
        }

        /**
         * @param Item $item
         * @param int  $id
         * @param int  $units
         *
         * @return Item
         */
        public static function updateAbilityDescription(Item $item, int $id, int $units): Item{
                $lore = $item->getLore();

                if(self::getItemAbility($item) !== null){
                        array_pop($lore);
                        array_pop($lore);
                }
                $lore_format = $lore_format ?? "&r&l&8» &r&7ability: &f@ability\n&r&l&8» &r&7duration: &f@units";
                $abilityLore = explode("\n", RandomUtils::colorMessage(str_replace(["@ability", "@units"], [self::abilityIdToName($id), $units], $lore_format)));

                $item->setLore(array_merge($lore, $abilityLore));
                return $item;
        }

        /**
         * @param Item        $item
         * @param int         $id
         * @param int         $units
         * @param string|null $lore_format
         *
         * @return Item
         */
        public static function setItemAbilityActive(Item $item, int $id, int $units, ?string $lore_format = null): Item{
                self::updateAbilityDescription($item, $id, $units);

                $item = self::setItemAbility($item, $id);
                $item = self::setItemAbilityDuration($item, $units);

                if($item->getNamedTag()->getTag(Item::TAG_ENCH) === null){
                        $item->getNamedTag()->setTag(Item::TAG_ENCH, new ListTag());
                }

                return $item;
        }

        /**
         * @param Item $item
         *
         * @return Item
         */
        public static function setItemAbilityInactive(Item $item): Item{
                $lore = $item->getLore();
                if(self::getItemAbility($item) !== null){
                        array_pop($lore);
                        array_pop($lore);
                }
                $item->setLore($lore);

                $item = self::removeItemAbility($item);
                $item = self::removeItemAbilityDuration($item);

                $tagEnch = $item->getNamedTag()->getTag(Item::TAG_ENCH);
                if($tagEnch !== null && count($tagEnch->getValue()) <= 0){
                        $item->getNamedTag()->removeTag(Item::TAG_ENCH);
                }

                return $item;
        }
}