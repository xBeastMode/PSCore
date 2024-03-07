<?php
namespace PrestigeSociety\Events\Events\Fun;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use PrestigeSociety\Events\CoreEvent;
class ToggleIgnoreEvent extends CoreEvent implements Cancellable{
        use CancellableTrait;

        /** @var Player */
        protected Player $player;
        /** @var Player */
        protected Player $target;
        /** @var bool */
        protected bool $enabled;

        /**
         * ToggleIgnoreEvent constructor.
         *
         * @param Player $player
         * @param Player $target
         * @param bool   $enabled
         */
        public function __construct(Player $player, Player $target, bool $enabled){
                $this->player = $player;
                $this->target = $target;
                $this->enabled = $enabled;
        }

        /**
         * @return Player
         */
        public function getPlayer(): Player{
                return $this->player;
        }

        /**
         * @return Player
         */
        public function getTarget(): Player{
                return $this->target;
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