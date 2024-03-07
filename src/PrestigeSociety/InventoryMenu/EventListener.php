<?php
namespace PrestigeSociety\InventoryMenu;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerQuitEvent;
use PrestigeSociety\Core\PrestigeSocietyCore;
class EventListener implements Listener{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * KitListener constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){;
                $this->core = $core;
        }

        /**
         * @param PlayerQuitEvent $event
         */
        public function onPlayerQuit(PlayerQuitEvent $event){
                $this->core->module_loader->inventory_menu->closeInventory($event->getPlayer());
        }

        /**
         * @param InventoryCloseEvent $event
         */
        public function onInventoryClose(InventoryCloseEvent $event){
                $this->core->module_loader->inventory_menu->closeInventory($event->getPlayer());
        }

        /**
         * @param InventoryTransactionEvent $event
         */
        public function onInventoryTransaction(InventoryTransactionEvent $event){
                $this->core->module_loader->inventory_menu->onInventoryTransaction($event);
        }

        /**
         * @param PlayerDropItemEvent $event
         */
        public function onPlayerDropItem(PlayerDropItemEvent $event){
                $this->core->module_loader->inventory_menu->onPlayerDropItem($event);
        }
}