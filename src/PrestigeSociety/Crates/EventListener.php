<?php

namespace PrestigeSociety\Crates;
use pocketmine\block\inventory\ChestInventory;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use PrestigeSociety\Core\PrestigeSocietyCore;
class EventListener implements Listener{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * EventListener constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param InventoryTransactionEvent $event
         */
        public function onInventoryTransaction(InventoryTransactionEvent $event){
                if($this->core->module_loader->crates->isSpinning($event->getTransaction()->getSource())){
                        $actions = $event->getTransaction()->getActions();
                        foreach($actions as $action){
                                if($action instanceof SlotChangeAction && $action->getInventory() instanceof ChestInventory){
                                        $event->cancel();
                                }
                        }
                }
        }

        /**
         * @param InventoryCloseEvent $event
         */
        public function onInventoryClose(InventoryCloseEvent $event){
                $player = $event->getPlayer();

                if($this->core->module_loader->crates->isSpinning($player)){
                        $this->core->module_loader->crates->endSpin($player, false);
                }
        }

}