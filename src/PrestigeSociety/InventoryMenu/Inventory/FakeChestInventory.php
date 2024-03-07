<?php
namespace PrestigeSociety\InventoryMenu\Inventory;
use pocketmine\block\inventory\ChestInventory;
use pocketmine\world\Position;
use pocketmine\world\sound\Sound;
class FakeChestInventory extends ChestInventory implements MenuInventory{
        /** @var Sound|null */
        protected ?Sound $open_sound = null;
        /** @var Sound|null */
        protected ?Sound $close_sound = null;

        /**
         * FakeDoubleChestInventory constructor.
         *
         * @param Position   $position
         * @param Sound|null $openSound
         * @param Sound|null $closeSound
         */
        public function __construct(Position $position, ?Sound $openSound = null, ?Sound $closeSound = null){
                parent::__construct($position);

                $this->open_sound = $openSound;
                $this->close_sound = $closeSound;
        }

        public function getOpenSound(): Sound{
                return $this->open_sound ?? parent::getOpenSound();
        }

        public function getCloseSound(): Sound{
                return $this->close_sound ?? parent::getOpenSound();
        }
}