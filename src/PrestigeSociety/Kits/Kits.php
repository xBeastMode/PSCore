<?php
namespace PrestigeSociety\Kits;
use DateTime;
use Exception;
use pocketmine\block\inventory\ChestInventory;
use pocketmine\block\inventory\DoubleChestInventory;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\StringToEffectParser;
use pocketmine\item\Item;
use pocketmine\item\Potion;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\ListTag;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Sounds\SoundFactory;
use PrestigeSociety\Core\Utils\ConsoleUtils;
use PrestigeSociety\Core\Utils\SoundNames;
use PrestigeSociety\Core\Utils\StringUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\DataModels\KitsCooldownModel;
use PrestigeSociety\Forms\FormList\Kits\ChooseKitForm;
use PrestigeSociety\Forms\FormList\Kits\ChooseKitOptionForm;
use PrestigeSociety\Forms\FormManager;
use PrestigeSociety\InventoryMenu\Inventory\MenuInventory;
use PrestigeSociety\InventoryMenu\TransactionData;
class Kits{
        const KIT_COOLDOWN_TAG = "kit_cooldown";

        const CLAIM_SUCCESS = 0;
        const CLAIM_NO_ITEMS = 1;
        const CLAIM_COOLDOWN = 2;
        const CLAIM_NO_SPACE = 3;

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        public int $CHOOSE_KIT_ID = 0;
        public int $CHOOSE_KIT_OPTION_ID = 0;

        /**
         * Kits constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                foreach($this->getKitNames() as $name){
                        PermissionManager::getInstance()->addPermission(new Permission("kit.$name"));
                }

                $this->CHOOSE_KIT_ID = FormManager::getNextFormId();
                $this->CHOOSE_KIT_OPTION_ID = FormManager::getNextFormId();

                $this->core->module_loader->form_manager->registerHandler($this->CHOOSE_KIT_ID, ChooseKitForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->CHOOSE_KIT_OPTION_ID, ChooseKitOptionForm::class);
        }

        /**
         * @param        $player
         * @param string $kit
         *
         * @return bool
         */
        public function isOnCoolDown($player, string $kit): bool{
                return $this->getCoolDown($player, $kit) > 0;
        }

        /**
         * @param        $player
         * @param string $kit
         * @param int    $time
         */
        public function setCoolDown($player, string $kit, int $time){
                KitsCooldownModel::query()->updateOrCreate([["name", "=", RandomUtils::getName($player)], ["kit", "=", $kit]], [
                    "name" => RandomUtils::getName($player),
                    "kit" => $kit,
                    "time_claimed" => time(),
                    "cooldown" => $time
                ]);
        }

        /**
         * @param        $player
         * @param string $kit
         *
         * @return bool
         */
        public function checkCoolDown($player, string $kit): bool{
                $record = KitsCooldownModel::query()->where([["name", "=", RandomUtils::getName($player)], ["kit", "=", $kit]]);
                if(!$record->exists()){
                        return false;
                }
                return ($record->value("cooldown") + $record->value("time_claimed")) <= time();
        }

        /**
         * @param        $player
         * @param string $kit
         *
         * @return int
         */
        public function getCoolDown($player, string $kit): int{
                $record = KitsCooldownModel::query()->where([["name", "=", RandomUtils::getName($player)], ["kit", "=", $kit]]);
                if(!$record->exists()){
                        return 0;
                }
                return ($record->value("cooldown") + $record->value("time_claimed")) - time();
        }

        /**
         * @param        $player
         * @param string $kit
         *
         * @return string[]
         */
        public function getCoolDownDHMS($player, string $kit): array{
                return StringUtils::secondsToDHMS($this->getCoolDown($player, $kit));
        }

        /**
         * @param        $player
         * @param string $kit
         */
        public function removeCoolDown($player, string $kit){
                $record = KitsCooldownModel::query()->where([["name", "=", RandomUtils::getName($player)], ["kit", "=", $kit]]);
                if(!$record->exists()){
                        return;
                }
                $record->delete();
        }

        /**
         * @param                $player
         * @param string         $contents
         * @param string         $kit
         */
        public function saveRawContents($player, string $contents, string $kit){
                KitsCooldownModel::query()->updateOrCreate([["name", "=", RandomUtils::getName($player)], ["kit", "=", $kit]], [
                    "name" => RandomUtils::getName($player),
                    "kit" => $kit,
                    "contents" => $contents,
                ]);
        }

        /**
         * @param        $player
         * @param array  $contents
         * @param string $kit
         */
        public function saveContents($player, array $contents, string $kit){
                $contents = array_filter($contents, function ($item){
                        return $item instanceof Item;
                });
                $this->saveRawContents($player, $this->serializeContents($contents), $kit);
        }

        /**
         * @param                                     $player
         * @param ChestInventory|DoubleChestInventory $inventory
         * @param string                              $kit
         */
        public function saveChestInventoryContents($player, ChestInventory|DoubleChestInventory $inventory, string $kit){
                $this->saveRawContents($player, $this->serializeContents($inventory->getContents()), $kit);
        }

        /**
         * @param        $player
         * @param string $kit
         *
         * @return string|null
         */
        public function getRawContents($player, string $kit): ?string{
                $record = KitsCooldownModel::query()->where([["name", "=", RandomUtils::getName($player)], ["kit", "=", $kit]]);

                if(!$record->exists()){
                        return null;
                }

                return $record->value("contents");
        }

        /**
         * @param        $player
         * @param string $kit
         *
         * @return array
         */
        public function getContents($player, string $kit): array{
                $contents = $this->getRawContents($player, $kit);
                return $contents !== null ? $this->deserializeContents($contents) : [];
        }

        /**
         * @param string $items
         *
         * @return Item[]
         */
        public function deserializeContents(string $items): array{
                return array_map(function (array $data){
                        return Item::jsonDeserialize($data);
                }, unserialize($items));
        }

        /**
         * @param array $items
         *
         * @return string
         */
        public function serializeContents(array $items): string{
                return serialize(array_map(function (Item $item){
                        return $item->jsonSerialize();
                }, $items));
        }

        /**
         * @param string $kit
         *
         * @return bool
         */
        public function kitExists(string $kit): bool{
                return isset($this->core->module_configurations->kits[$kit]);
        }

        /**
         * @return string[]
         */
        public function getKitNames(): array{
                return array_keys($this->core->module_configurations->kits);
        }

        /**
         * @param string $kit
         *
         * @return Item[]
         */
        public function getKitItems(string $kit): array{
                $data = $this->core->module_configurations->kits[$kit] ?? null;

                if($data === null){
                        return [];
                }

                $items = RandomUtils::parseItemsWithEnchantments([$data["helmet"], $data["chest"], $data["legs"], $data["boots"]]);
                return array_merge($items, RandomUtils::parseItemsWithEnchantments($data["items"]));
        }

        /**
         * @param string $kit
         *
         * @return null|string
         */
        public function getKitEffectItemFormat(string $kit): ?string{
                $data = $this->core->module_configurations->kits[$kit] ?? null;

                if($data === null){
                        return null;
                }

                return $data["effect_item_format"];
        }

        /**
         * @param string $kit
         *
         * @return EffectInstance[]
         */
        public function getKitEffects(string $kit): array{
                $data = $this->core->module_configurations->kits[$kit] ?? null;

                if($data === null){
                        return [];
                }

                return RandomUtils::parseEffects($data["effects"]);
        }

        /**
         * @param int $id
         *
         * @return string
         */
        public function effectIdToName(int $id): string{
                return RandomUtils::effectIdToName($id);
        }

        /**
         * @param string $kit
         * @param bool   $identifiers
         *
         * @return Item[]
         */
        public function getKitEffectsAsItems(string $kit, bool $identifiers = true): array{
                $output = [];

                foreach(self::getKitEffects($kit) as $effect){
                        /** @var Potion $potion */
                        $potion = VanillaItems::DRAGON_BREATH();

                        $effect_item_format = self::getKitEffectItemFormat($kit);
                        $effect_item_format = str_replace([
                            "@effect",
                            "@duration",
                            "@amplifier"
                        ], [
                            strtoupper(RandomUtils::getNameFromTranslatable($effect)),
                            round($effect->getDuration() / 20),
                            $effect->getAmplifier(),
                        ], $effect_item_format);

                        $potion->setCustomName(RandomUtils::colorMessage($effect_item_format));
                        $potion->getNamedTag()->setTag("ench", new ListTag());

                        if($identifiers){
                                $potion->getNamedTag()->setString("__effect", RandomUtils::getNameFromTranslatable($effect));
                                $potion->getNamedTag()->setInt("__duration", $effect->getDuration());
                                $potion->getNamedTag()->setInt("__amplifier", $effect->getAmplifier());
                        }

                        $output[] = $potion;
                }

                return $output;
        }

        /**
         * @param string $kit
         *
         * @return null|Item
         *
         * @throws Exception
         */
        public function getKitDisplayItem(string $kit): ?Item{
                $data = $this->core->module_configurations->kits[$kit] ?? null;

                if($data === null){
                        return null;
                }

                return RandomUtils::parseItemsWithEnchantments([$data["display_item"]])[0];
        }

        /**
         * @param string $kit
         *
         * @return array
         */
        public function getKitCommands(string $kit): array{
                $data = $this->core->module_configurations->kits[$kit] ?? null;

                if($data === null){
                        return [];
                }

                return $data["commands"];
        }

        /**
         * @param string $kit
         *
         * @return null|string[]
         *
         * @throws Exception
         */
        public function getKitDescription(string $kit): ?array{
                $data = $this->core->module_configurations->kits[$kit] ?? null;

                if($data === null){
                        return null;
                }

                return $data["description"];
        }

        /**
         * @param string $kit
         *
         * @return null|string[]
         *
         * @throws Exception
         */
        public function getKitCooldownDescription(string $kit): ?array{
                $data = $this->core->module_configurations->kits[$kit] ?? null;

                if($data === null){
                        return null;
                }

                return $data["cooldown_description"];
        }

        /**
         * @param string $kit
         *
         * @return int
         *
         * @throws Exception
         */
        public function getKitCooldown(string $kit): int{
                $data = $this->core->module_configurations->kits[$kit] ?? null;

                if($data === null){
                        return 0;
                }

                /** @var DateTime $time */
                $time = StringUtils::stringToTimestamp($data["cooldown"])[0];
                return $time->getTimestamp() - time();
        }

        /**
         * @param string $kit
         *
         * @return array|null
         *
         * @throws Exception
         */
        public function getKitCooldownDHMS(string $kit): ?array{
                $cooldown = $this->getKitCooldown($kit);

                if($cooldown === null){
                        return null;
                }

                return StringUtils::secondsToDHMS($cooldown);
        }

        /**
         * @param Player $player
         * @param string $kit
         * @param bool   $force
         *
         * @return int
         *
         * @throws Exception
         */
        public function claimKit(Player $player, string $kit, bool $force = false): int{
                $items = $this->getKitItems($kit);

                if(count($items) <= 0){
                        return self::CLAIM_NO_ITEMS;
                }

                if($this->isOnCoolDown($player, $kit) && !$force){
                        return self::CLAIM_COOLDOWN;
                }

                $contents_added = [];
                $armor = array_splice($items, 0, 4);

                if(count($player->getArmorInventory()->getContents()) <= 0){
                        $player->getArmorInventory()->setContents($armor);
                        $contents_added = $armor;
                }else{
                        $items = array_merge($armor, $items);
                }

                foreach($items as $item){
                        $contents_added []= $item;
                        if(!$player->getInventory()->canAddItem($item)){
                                $player->getInventory()->removeItem(...$contents_added);
                                return self::CLAIM_NO_SPACE;
                        }
                        $player->getInventory()->addItem($item);
                }

                foreach(self::getKitEffects($kit) as $effect) $player->getEffects()->add($effect);

                $commands = $this->getKitCommands($kit);
                foreach($commands as $command){
                        ConsoleUtils::dispatchCommandAsConsole(str_replace("@player", $player->getName(), $command));
                }

                $this->setCoolDown($player, $kit, $this->getKitCooldown($kit));
                return self::CLAIM_SUCCESS;
        }

        /**
         * @param Player $player
         * @param string $kit
         */
        protected function onInventoryClaim(Player $player, string $kit){
                $timestamp_string = ["@days", "@hours", "@minutes", "@seconds"];
                $timestamp_values = $this->getCoolDownDHMS($player, $kit);

                $message = $this->core->getMessage("kits", "claim_message");
                $message = str_replace($timestamp_string, $timestamp_values, $message);

                $commands = $this->core->module_loader->kits->getKitCommands($kit);
                foreach($commands as $command){
                        ConsoleUtils::dispatchCommandAsConsole(str_replace("@player", $player->getName(), $command));
                }

                $player->sendMessage(RandomUtils::colorMessage($message));
        }

        /**
         * @param Player $player
         * @param string $kit
         * @param bool   $run_commands
         *
         * @return bool
         *
         * @throws Exception
         */
        public function openClaimInventory(Player $player, string $kit, bool $run_commands = true): bool{
                if(!$this->kitExists($kit)){
                        return false;
                }

                $cooldown = $this->getCoolDown($player, $kit);
                $save = true;

                $fill = function (MenuInventory $inventory) use ($player, $kit, &$save){
                        $item = $this->getKitDisplayItem($kit);

                        $description = $this->getKitCooldownDescription($kit);
                        $dhms = $this->getCoolDownDHMS($player, $kit);

                        $description = array_map(function ($description) use ($kit, $dhms){
                                return RandomUtils::colorMessage(str_replace(["@days", "@hours", "@minutes", "@seconds"], $dhms, $description));
                        }, $description);

                        $item->setLore($description);
                        $item->getNamedTag()->setByte(self::KIT_COOLDOWN_TAG, 1);

                        for($i = 0; $i < $inventory->getSize(); $i++){
                                $inventory->setItem($i, $item);
                        }

                        $save = false;
                };

                $chest_inventory = $this->core->module_loader->inventory_menu->openDoubleChestInventory($player, function (TransactionData $data) use ($kit, $fill){
                        $source_item = $data->source_item;
                        $player = $data->player;
                        $inventory = $data->inventory;

                        if($source_item->getNamedTag()->getByte(self::KIT_COOLDOWN_TAG, false) != false){
                                RandomUtils::playSound(SoundNames::SOUND_NOTE_BASS, $data->player);
                                return true;
                        }

                        if(($effect = $source_item->getNamedTag()->getString("__effect", false)) != false){
                                $effect = StringToEffectParser::getInstance()->parse($effect);
                                $effect_instance = new EffectInstance($effect,
                                    $source_item->getNamedTag()->getInt("__duration", 0),
                                    $source_item->getNamedTag()->getInt("__amplifier", 0)
                                );

                                $player->getEffects()->add($effect_instance);
                                $inventory->remove($source_item);

                                if(count($inventory->getContents()) <= 0){
                                        $fill($inventory);
                                }

                                RandomUtils::playSound(SoundNames::SOUND_RANDOM_DRINK, $data->player);
                                return true;
                        }

                        if(count($inventory->getContents()) <= 1){
                                $player->getInventory()->addItem($source_item);

                                $inventory->remove($source_item);
                                $fill($inventory);

                                RandomUtils::playSound(SoundNames::SOUND_RANDOM_POP, $data->player);
                                return true;
                        }

                        return false;
                }, [
                    "height" => 5,
                    "title" => RandomUtils::colorMessage("&l&8CLAIM " . strtoupper($kit) . " KIT"),
                    "open_sound" => SoundFactory::ENDERCHEST_OPEN_SOUND(),
                    "close_sound" => SoundFactory::ENDERCHEST_CLOSE_SOUND()
                ]);

                $contents = $this->getContents($player, $kit);

                $this->core->module_loader->inventory_menu->setCloseCallback($player, function () use ($player, $kit, &$chest_inventory, &$save){
                        if($save){
                                $this->saveChestInventoryContents($player, $chest_inventory, $kit);
                        }else{
                                $this->saveContents($player, [], $kit);
                        }
                });

                if($cooldown > 0){
                        if(count($contents) <= 0){
                                $fill($chest_inventory);
                                return true;
                        }
                        $chest_inventory->setContents($contents);
                        return true;
                }

                $contents = array_merge($this->getKitItems($kit), $this->getKitEffectsAsItems($kit));
                $chest_inventory->setContents($contents);

                $this->setCoolDown($player, $kit, $this->getKitCooldown($kit));

                if($run_commands){
                        $this->onInventoryClaim($player, $kit);
                }
                return true;
        }

        /**
         * @param Player $player
         * @param string $kit
         *
         * @return bool
         *
         * @throws Exception
         */
        public function openViewInventory(Player $player, string $kit): bool{
                if(!$this->kitExists($kit)){
                        return false;
                }

                $chest_inventory = $this->core->module_loader->inventory_menu->openDoubleChestInventory($player, function (TransactionData $data) use ($kit){
                        RandomUtils::playSound(SoundNames::SOUND_NOTE_BASS, $data->player);
                        return true;
                }, [
                    "height" => 5,
                    "title" => RandomUtils::colorMessage("&l&8" . strtoupper($kit) . " KIT"),
                    "open_sound" => SoundFactory::ENDERCHEST_OPEN_SOUND(),
                    "close_sound" => SoundFactory::ENDERCHEST_CLOSE_SOUND()
                ]);

                $contents = array_merge($this->getKitItems($kit), $this->getKitEffectsAsItems($kit));
                $chest_inventory->setContents($contents);

                return true;
        }
}