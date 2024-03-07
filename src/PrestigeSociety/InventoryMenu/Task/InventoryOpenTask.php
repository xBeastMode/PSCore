<?php
namespace PrestigeSociety\InventoryMenu\Task;
use pocketmine\block\inventory\DoubleChestInventory;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
class InventoryOpenTask extends Task{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;
        /** @var Player */
        protected Player $player;
        /** @var DoubleChestInventory */
        protected DoubleChestInventory $chest_inventory;

        /**
         * InventoryOpenTask constructor.
         *
         * @param PrestigeSocietyCore  $core
         * @param Player               $player
         * @param DoubleChestInventory $chestInventory
         */
        public function __construct(PrestigeSocietyCore $core, Player $player, DoubleChestInventory $chestInventory){
                $this->core = $core;
                $this->player = $player;
                $this->chest_inventory = $chestInventory;
        }

        /**
         * Actions to execute when run
         *
         * @return void
         */
        public function onRun(): void{
                if($this->player->isConnected()){
                        $this->player->setCurrentWindow($this->chest_inventory);
                }else{
                        $this->core->module_loader->inventory_menu->closeInventory($this->player);
                }
        }
}