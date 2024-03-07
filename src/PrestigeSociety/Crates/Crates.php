<?php
namespace PrestigeSociety\Crates;
use pocketmine\block\inventory\ChestInventory;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;
use pocketmine\scheduler\TaskHandler;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Crates\Task\CrateUpdateTask;
use PrestigeSociety\Forms\FormList\Crates\BuyCratesForm;
use PrestigeSociety\Forms\FormList\Crates\ConfirmOpenForm;
use PrestigeSociety\Forms\FormList\Crates\ConfirmPurchaseForm;
use PrestigeSociety\Forms\FormList\Crates\CratesForm;
use PrestigeSociety\Forms\FormList\Crates\OpenCratesForm;
use PrestigeSociety\Forms\FormManager;
use PrestigeSociety\InventoryMenu\TransactionData;
use PrestigeSociety\Player\Data\Settings;

class Crates{
        const TYPE_BASIC_CRATE = "basic_crate";
        const TYPE_OP_CRATE = "op_crate";
        const TYPE_EXCLUSIVE_CRATE = "exclusive_crate";
        const TYPE_VOTE_CRATE = "vote_crate";
        const TYPE_WEAPON_CRATE = "weapon_crate";

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        public int $CRATES_ID = 0;
        public int $OPEN_CRATES_ID = 0;
        public int $BUY_CRATES_ID = 0;
        public int $CONFIRM_PURCHASE_ID = 0;
        public int $CONFIRM_OPEN_ID = 0;

        /** @var string[][]|int[][]|TaskHandler[][] */
        protected array $players_spinning = [];

        /**
         * Crates constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                $this->CRATES_ID = FormManager::getNextFormId();
                $this->OPEN_CRATES_ID = FormManager::getNextFormId();
                $this->BUY_CRATES_ID = FormManager::getNextFormId();
                $this->CONFIRM_PURCHASE_ID = FormManager::getNextFormId();
                $this->CONFIRM_OPEN_ID = FormManager::getNextFormId();

                $this->core->module_loader->form_manager->registerHandler($this->CRATES_ID, CratesForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->OPEN_CRATES_ID, OpenCratesForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->BUY_CRATES_ID, BuyCratesForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->CONFIRM_PURCHASE_ID, ConfirmPurchaseForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->CONFIRM_OPEN_ID, ConfirmOpenForm::class);

                $core->getServer()->getPluginManager()->registerEvents(new EventListener($core), $core);
        }

        /**
         * @param string $message
         */
        public function broadcastMessage(string $message){
                foreach($this->core->getServer()->getOnlinePlayers() as $player){
                        if($this->core->module_loader->player_data->getPlayerSettings($player)->get(Settings::SETTING_SUPPRESS_CRATE_MESSAGES, true)) continue;

                        $player->sendMessage($message);
                }
        }

        /**
         * @param string $type
         *
         * @return bool
         */
        public function validateCrateType(string $type): bool{
                return in_array($type, [
                    self::TYPE_BASIC_CRATE,
                    self::TYPE_OP_CRATE,
                    self::TYPE_EXCLUSIVE_CRATE,
                    self::TYPE_VOTE_CRATE,
                    self::TYPE_WEAPON_CRATE
                ]);
        }

        /**
         * @param $player
         *
         * @return bool
         */
        public function playerExists($player): bool{
                return StaticCrates::playerExists($player);
        }

        /**
         * @param $player
         */
        public function addNewPlayer($player): void{
                StaticCrates::addNewPlayer($player);
        }

        /**
         * @param        $player
         * @param string $type
         *
         * @return int
         */
        public function getCrateCount($player, string $type): int{
                return StaticCrates::getCrateCount($player, $type);
        }

        /**
         * @param        $player
         * @param string $type
         * @param int    $count
         *
         * @return bool
         */
        public function setCrateCount($player, string $type, int $count): bool{
                return StaticCrates::setCrateCount($player, $type, $count);
        }

        /**
         * @param        $player
         * @param string $type
         * @param int    $count
         *
         * @return bool
         */
        public function addCrateCount($player, string $type, int $count = 1): bool{
                return StaticCrates::addCrateCount($player, $type, $count);
        }

        /**
         * @param        $player
         * @param string $type
         * @param int    $count
         *
         * @return bool
         */
        public function subtractCrateCount($player, string $type, int $count = 1): bool{
                return StaticCrates::subtractCrateCount($player, $type, $count);
        }

        /**
         * @param Player $player
         *
         * @return bool
         */
        public function isSpinning(Player $player): bool{
                return isset($this->players_spinning[spl_object_hash($player)]);
        }

        /**
         * @param Player $player
         * @param string $type
         * @param int    $subtract_amount
         *
         * @return bool
         */
        public function openCrate(Player $player, string $type, int $subtract_amount = 1): bool{
                $event = $this->core->module_loader->events->onCrateStartSpin($player, $type);

                if(!$event->isCancelled()){
                        $crate = $this->core->module_configurations->crates[$event->getCrate()];
                        $chest_inventory = $this->core->module_loader->inventory_menu->openInventory($player, function (TransactionData $data){
                                return true;
                        }, [
                            "title" => RandomUtils::colorMessage("&8&l" . strtoupper($crate["name"]) . " CRATE"),
                            "height" => 5,
                        ]);

                        if($chest_inventory instanceof ChestInventory){
                                $exempt = [13];
                                $crystal = [4, 12, 14, 22];

                                for($i = 0; $i < $chest_inventory->getSize(); $i++){
                                        if(in_array($i, $exempt)) continue;
                                        if(in_array($i, $crystal)){
                                                $chest_inventory->setItem($i, VanillaBlocks::END_STONE()->asItem()->setCustomName(""));
                                                continue;
                                        }

                                        $map = VanillaBlocks::COBWEB()->asItem()->setCustomName("");
                                        $chest_inventory->setItem($i, $map);
                                }

                                $items = RandomUtils::parseItemsWithEnchantments($crate["items"]);
                                $commands = $crate["commands"];
                                $commands = array_map(function ($value){
                                        return explode(";", $value);
                                }, $commands);

                                $message = $this->core->getMessage("crates", "begin_spin");
                                $message = str_replace(["@player", "@crate"], [$player->getName(), $crate["name"]], $message);
                                $this->broadcastMessage(RandomUtils::colorMessage($message));

                                $handler = $this->core->getScheduler()->scheduleRepeatingTask($task = new CrateUpdateTask($this->core, $player, $chest_inventory, $items, $commands), 4);
                                $task->setHandler($handler);

                                $this->players_spinning[spl_object_hash($player)] = [$type, $subtract_amount, $handler];
                                return true;
                        }
                }
                return false;
        }

        /**
         * @param Player $player
         * @param bool   $safely
         */
        public function endSpin(Player $player, bool $safely = true): void{
                if($this->isSpinning($player)){
                        $spinning = $this->players_spinning[spl_object_hash($player)];
                        $this->core->module_loader->events->onCrateEndSpin($player, $spinning[0]);

                        $this->core->module_loader->inventory_menu->setCloseCallback($player, function () use (&$player, $safely, $spinning){
                                if($safely) $this->subtractCrateCount($player, $spinning[0], $spinning[1]);
                        });

                        $this->core->module_loader->inventory_menu->closeInventory($player);
                        if(!$safely) $spinning[2]->cancel();

                        unset($this->players_spinning[spl_object_hash($player)]);
                }
        }
}