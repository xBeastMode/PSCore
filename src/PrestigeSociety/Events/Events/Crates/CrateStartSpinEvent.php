<?php
namespace PrestigeSociety\Events\Events\Crates;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use PrestigeSociety\Events\CoreEvent;
class CrateStartSpinEvent extends CoreEvent implements Cancellable{
        use CancellableTrait;

        /** @var Player */
        protected Player $player;
        /** @var string */
        protected string $crate;

        /**
         * CrateStartSpinEvent constructor.
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

        /**
         * @param string $crate
         */
        public function setCrate(string $crate): void{
                $this->crate = $crate;
        }
}