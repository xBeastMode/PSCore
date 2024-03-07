<?php
namespace PrestigeSociety\Management;
use JetBrains\PhpStorm\Pure;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\StringUtils;
use PrestigeSociety\Forms\FormList\Management\AddSlotForm;
use PrestigeSociety\Forms\FormList\Management\ConfirmAddSlotForm;
use PrestigeSociety\Forms\FormList\Management\ConfirmSetAbilityForm;
use PrestigeSociety\Forms\FormList\Management\ManageAbilityForm;
use PrestigeSociety\Forms\FormList\Management\ManagementForm;
use PrestigeSociety\Forms\FormList\Management\ManageSlotsForm;
use PrestigeSociety\Forms\FormList\Management\RemoveAbilityForm;
use PrestigeSociety\Forms\FormList\Management\RemoveSlotForm;
use PrestigeSociety\Forms\FormList\Management\SetAbilityForm;
use PrestigeSociety\Forms\FormList\Management\UnlockSlotForm;
use PrestigeSociety\Forms\FormList\Management\UpgradeSlotForm;
use PrestigeSociety\Forms\FormList\Management\ViewSlotForm;
use PrestigeSociety\Forms\FormManager;
class Management{
        const ABILITY_TAG = "__ability__";
        const ABILITY_DURATION_TAG = "__ability_duration__";

        const MAX_SLOTS_TAG = "__max_slots__";
        const DEFAULT_MAX_SLOTS = 3;

        const ABILITY_MONEY_BOOST = 0;
        const ABILITY_DOUBLE_BOSS_REWARD = 1;

        const ENCHANTMENTS_VANILLA = "vanilla";
        const ENCHANTMENTS_CUSTOM = "custom";

        public int $MANAGEMENT_ID = 0;
        public int $MANAGE_ABILITY_ID = 0;
        public int $MANAGE_SLOTS_ID = 0;
        public int $ADD_SLOT_ID = 0;
        public int $CONFIRM_ADD_SLOT_ID = 0;
        public int $VIEW_SLOT_ID = 0;
        public int $UPGRADE_SLOT_ID = 0;
        public int $REMOVE_SLOT_ID = 0;
        public int $SET_ABILITY_ID = 0;
        public int $CONFIRM_SET_ABILITY_ID = 0;
        public int $REMOVE_ABILITY_ID = 0;
        public int $UNLOCK_SLOT_ID = 0;

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * Management constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                $this->MANAGEMENT_ID = FormManager::getNextFormId();
                $this->MANAGE_ABILITY_ID = FormManager::getNextFormId();
                $this->MANAGE_SLOTS_ID = FormManager::getNextFormId();
                $this->ADD_SLOT_ID = FormManager::getNextFormId();
                $this->CONFIRM_ADD_SLOT_ID = FormManager::getNextFormId();
                $this->VIEW_SLOT_ID = FormManager::getNextFormId();
                $this->UPGRADE_SLOT_ID = FormManager::getNextFormId();
                $this->REMOVE_SLOT_ID = FormManager::getNextFormId();
                $this->SET_ABILITY_ID = FormManager::getNextFormId();
                $this->CONFIRM_SET_ABILITY_ID = FormManager::getNextFormId();
                $this->REMOVE_ABILITY_ID = FormManager::getNextFormId();
                $this->UNLOCK_SLOT_ID = FormManager::getNextFormId();

                $this->core->module_loader->form_manager->registerHandler($this->MANAGEMENT_ID, ManagementForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->MANAGE_ABILITY_ID, ManageAbilityForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->MANAGE_SLOTS_ID, ManageSlotsForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->ADD_SLOT_ID, AddSlotForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->CONFIRM_ADD_SLOT_ID, ConfirmAddSlotForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->VIEW_SLOT_ID, ViewSlotForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->UPGRADE_SLOT_ID, UpgradeSlotForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->REMOVE_SLOT_ID, RemoveSlotForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->SET_ABILITY_ID, SetAbilityForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->CONFIRM_SET_ABILITY_ID, ConfirmSetAbilityForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->REMOVE_ABILITY_ID, RemoveAbilityForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->UNLOCK_SLOT_ID, UnlockSlotForm::class);
        }

        /**
         * @param Item $item
         *
         * @return int|null
         */
        public function getItemAbility(Item $item): ?int{
                return StaticManagement::getItemAbility($item);
        }

        /**
         * @param Item $item
         *
         * @return int
         */
        public function getItemAbilityDuration(Item $item): int{
                return StaticManagement::getItemAbilityDuration($item);
        }

        /**
         * @param Item $item
         * @param int  $id
         *
         * @return Item
         */
        public function setItemAbility(Item $item, int $id): Item{
                return StaticManagement::setItemAbility($item, $id);
        }

        /**
         * @param Item $item
         * @param int  $duration
         *
         * @return Item
         */
        public function setItemAbilityDuration(Item $item, int $duration): Item{
                return StaticManagement::setItemAbilityDuration($item, $duration);
        }

        /**
         * @param Item $item
         *
         * @return Item
         */
        public function removeItemAbility(Item $item): Item{
                return StaticManagement::removeItemAbility($item);
        }

        /**
         * @param Item $item
         * @return Item
         */
        public function removeItemAbilityDuration(Item $item): Item{
                return StaticManagement::removeItemAbilityDuration($item);
        }

        /**
         * @param Item $item
         *
         * @return int
         */
        public function getItemMaxSlots(Item $item): int{
                return StaticManagement::getItemMaxSlots($item);
        }

        /**
         * @param Item $item
         * @param int  $slots
         *
         * @return Item
         */
        public function setItemMaxSlots(Item $item, int $slots): Item{
                return StaticManagement::setItemMaxSlots($item, $slots);
        }

        /**
         * @param Item $item
         * @param int  $slots
         *
         * @return Item
         */
        public function addItemMaxSlots(Item $item, int $slots = 1): Item{
                return StaticManagement::addItemMaxSlots($item, $slots);
        }

        /**
         * @param int $id
         *
         * @return null|string
         */
        public function abilityIdToName(int $id): ?string{
                return StaticManagement::abilityIdToName($id);
        }

        /**
         * @param Item $item
         *
         * @return array
         */
        #[Pure] public function getAvailableAbilities(Item $item): array{
                return StaticManagement::getAvailableAbilities($item);
        }

        /**
         * @param int $id
         *
         * @return int|null
         */
        public function getAbilityCost(int $id): ?int{
                return $this->core->module_configurations->management["abilities"][$id]["cost"] ?? null;
        }

        /**
         * @param int $id
         *
         * @return int|null
         */
        public function getAbilityUnit(int $id): ?int{
                return $this->core->module_configurations->management["abilities"][$id]["unit"] ?? null;
        }

        /**
         * @param int $id
         *
         * @return int|null
         */
        public function getAbilityMaxUnits(int $id): ?int{
                return $this->core->module_configurations->management["abilities"][$id]["max_units"] ?? null;
        }

        /**
         * @param Item        $item
         * @param int         $id
         * @param int         $units
         * @param string|null $lore_format
         *
         * @return Item
         */
        public function setItemAbilityActive(Item $item, int $id, int $units, ?string $lore_format = null): Item{
                return StaticManagement::setItemAbilityActive($item, $id, $units, $lore_format);
        }

        /**
         * @param Item $item
         *
         * @return Item
         */
        public function setItemAbilityInactive(Item $item): Item{
                return StaticManagement::setItemAbilityInactive($item);
        }

        /**
         * @return int|null
         */
        public function getBoostCost(): ?int{
                return $this->core->module_configurations->management["enchantments"]["boost_cost"] ?? null;
        }

        /**
         * @return int|null
         */
        public function getBoostChance(): ?int{
                return $this->core->module_configurations->management["enchantments"]["boost_chance"] ?? null;
        }

        /**
         * @return int|null
         */
        public function getUnlockCost(): ?int{
                return $this->core->module_configurations->management["enchantments"]["unlock_cost"] ?? null;
        }

        /**
         * @return int|null
         */
        public function getMaxSlots(): ?int{
                return $this->core->module_configurations->management["enchantments"]["max_slots"] ?? null;
        }

        /**
         * @param string $enchantment_type
         *
         * @return int|null
         */
        public function getEnchantmentCost(string $enchantment_type): ?int{
                return $this->core->module_configurations->management["enchantments"][$enchantment_type]["cost"] ?? null;
        }

        /**
         * @param Item   $item
         * @param string $enchantment_type
         *
         * @return EnchantmentInstance[]
         */
        public function getCompatibleEnchantments(Item $item, string $enchantment_type): array{
                $enchantment_data = $this->core->module_configurations->management["enchantments"][$enchantment_type];
                $enchantments = [];

                foreach($enchantment_data["items"] as $enchantment_datum){
                        if($enchantment_datum["items"] !== "*"){
                                if(!in_array($item->getId(), $enchantment_datum["items"])) continue;
                        }
                        $enchantments = array_merge($enchantments, array_map(function ($enchantment_id){
                                return new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(StringUtils::stringToInteger($enchantment_id)));
                        }, $enchantment_datum["enchantments"]));
                }

                return $enchantments;
        }
}