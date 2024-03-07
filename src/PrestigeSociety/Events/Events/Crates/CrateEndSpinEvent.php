<?php
namespace PrestigeSociety\Events\Events\Crates;
use pocketmine\player\Player;
use PrestigeSociety\Events\CoreEvent;
class CrateEndSpinEvent extends CoreEvent{
        /** @var Player */
        protected Player $player;
        /** @var string */
        protected string $crate;

        /**
         * CrateEndSpinEvent constructor.
         *
         * @param Player $player
         * @param string $crate
         */
        public function __construct(Player $player, string $crate){
                $this->player = $player;
                $this->crate = $crate;
        }

        /**
         * @return Player
         */
        public function getPlayer(): Player{
                return $this->player;
        }

        /**
         * @return string
         */
        public function getCrate(): string{
                return $this->crate;
        }
}