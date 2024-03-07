<?php
namespace PrestigeSociety\InventoryMenu;
use pocketmine\item\Item;
use pocketmine\player\Player;
use PrestigeSociety\InventoryMenu\Inventory\MenuInventory;
class TransactionData{
        /** @var Player */
        public Player $player;
        /** @var MenuInventory */
        public MenuInventory $inventory;
        /** @var int */
        public int $slot;
        /** @var Item */
        public Item $source_item;
        /** @var Item */
        public Item $target_item;

        /**
         * TransactionData constructor.
         *
         * @param Player        $player
         * @param MenuInventory $inventory
         * @param int           $slot
         * @param Item          $source_item
         * @param Item          $target_item
         */
        public function __construct(Player $player, MenuInventory $inventory, int $slot, Item $source_item, Item $target_item){
                $this->player = $player;
                $this->inventory = $inventory;
                $this->slot = $slot;
                $this->source_item = $source_item;
                $this->target_item = $target_item;
        }
}