<?php
namespace PrestigeSociety\Events\Events\Fun;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use PrestigeSociety\Events\CoreEvent;
class ToggleLSDEvent extends CoreEvent implements Cancellable{
        use CancellableTrait;
        
        /** @var Player */
        protected Player $player;
        /** @var bool */
        protected bool $enabled;

        /**
         * ToggleLSDEvent constructor.
         *
         * @param Player $player
         * @param bool   $enabled
         */
        public function __construct(Player $player, bool $enabled){
                $this->player = $player;
                $this->enabled = $enabled;
        }

        /**
         * @return Player
         */
        public function getPlayer(): Player{
                return $this->player;
        }

        /**
         * @return bool
         */
        public function isEnabled(): bool{
                return $this->enabled;
        }

        /**
         * @param bool $enabled
         */
        public function setEnabled(bool $enabled): void{
                $this->enabled = $enabled;
        }
}