<?php
namespace PrestigeSociety\Casino;
use pocketmine\block\inventory\ChestInventory;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;
use PrestigeSociety\Casino\Task\SlotMachineUpdateTask;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormList\Casino\CashFlipForm;
use PrestigeSociety\Forms\FormList\Casino\CashFlipResultForm;
use PrestigeSociety\Forms\FormList\Casino\CasinoForm;
use PrestigeSociety\Forms\FormList\Casino\SlotMachinesForm;
use PrestigeSociety\Forms\FormList\Casino\SlotMachineSpinForm;
use PrestigeSociety\Forms\FormManager;
use PrestigeSociety\InventoryMenu\TransactionData;
use PrestigeSociety\Player\Data\Settings;
class Casino{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var int[] */
        protected array $casino_menu = [];

        public int $CASH_FLIP_ID = 0;
        public int $CASH_FLIP_RESULT_ID = 0;
        public int $CASINO_ID = 0;
        public int $SLOT_MACHINES_ID = 0;
        public int $SLOT_MACHINE_SPIN_ID = 0;

        /**
         * Casino constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                $this->CASH_FLIP_ID = FormManager::getNextFormId();
                $this->CASH_FLIP_RESULT_ID = FormManager::getNextFormId();
                $this->CASINO_ID = FormManager::getNextFormId();
                $this->SLOT_MACHINES_ID = FormManager::getNextFormId();
                $this->SLOT_MACHINE_SPIN_ID = FormManager::getNextFormId();

                $core->module_loader->form_manager->registerHandler($this->CASH_FLIP_ID, CashFlipForm::class);
                $core->module_loader->form_manager->registerHandler($this->CASH_FLIP_RESULT_ID, CashFlipResultForm::class);
                $core->module_loader->form_manager->registerHandler($this->CASINO_ID, CasinoForm::class);
                $core->module_loader->form_manager->registerHandler($this->SLOT_MACHINES_ID, SlotMachinesForm::class);
                $core->module_loader->form_manager->registerHandler($this->SLOT_MACHINE_SPIN_ID, SlotMachineSpinForm::class);

                $this->core->getServer()->getPluginManager()->registerEvents(new EventListener($core), $core);
        }

        /**
         * @param string $message
         */
        public function broadcastMessage(string $message){
                foreach($this->core->getServer()->getOnlinePlayers() as $player){
                        if($this->core->module_loader->player_data->getPlayerSettings($player)->get(Settings::SETTING_SUPPRESS_CASINO_MESSAGES, true)) continue;

                        $player->sendMessage($message);
                }
        }

        /**
         * @param Player $player
         *
         * @return bool
         */
        public function isSpinning(Player $player): bool{
                return isset($this->casino_menu[spl_object_hash($player)]);
        }

        /**
         * @param Player $player
         * @param int    $machine
         *
         * @return bool
         */
        public function spin(Player $player, int $machine): bool{
                $event = $this->core->module_loader->events->onCasinoStartSpin($player, $machine);
                if(!$event->isCancelled()){
                        $machine = $event->getMachine();
                        $machine_data = $this->core->module_configurations->casino["slot_machines"][$machine];

                        $chest_inventory = $this->core->module_loader->inventory_menu->openInventory($player, function (TransactionData $data){
                                return true;
                        }, [
                            "height" => 5,
                            "title" => RandomUtils::colorMessage("&8&l" . strtoupper($machine_data["name"]) . " SLOT MACHINE")
                        ]);

                        if($chest_inventory instanceof ChestInventory){
                                $exempt = [12, 13, 14];

                                for($i = 0; $i < $chest_inventory->getSize(); $i++){
                                        if(in_array($i, $exempt)) continue;
                                        $chest_inventory->setItem($i, VanillaBlocks::COBWEB()->asItem());
                                }

                                $items = RandomUtils::parseItemsWithEnchantments($machine_data["items"]);
                                $this->core->getScheduler()->scheduleRepeatingTask(new SlotMachineUpdateTask($this->core, $player, $chest_inventory, $items, $machine_data["commands"], $machine_data["name"]), 4);

                                $this->casino_menu[spl_object_hash($player)] = $machine;
                                return true;
                        }
                }
                return false;
        }

        /**
         * @param Player $player
         * @param bool   $won
         */
        public function endSpin(Player $player, bool $won){
                if($this->isSpinning($player)){
                        $this->core->module_loader->events->onCasinoEndSpin($player, $this->casino_menu[spl_object_hash($player)], $won);
                        $this->core->module_loader->inventory_menu->closeInventory($player);
                }
        }

}