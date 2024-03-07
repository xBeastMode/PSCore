<?php
namespace PrestigeSociety\Events\Events\Casino;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use PrestigeSociety\Events\CoreEvent;
class CasinoStartSpinEvent extends CoreEvent implements Cancellable{
        use CancellableTrait;

        /** @var Player */
        protected Player $player;
        /** @var int */
        protected int $machine;

        /**
         * CasinoStartSpinEvent constructor.
         *
         * @param Player $player
         * @param int    $machine
         */
        public function __construct(Player $player, int $machine){
                $this->player = $player;
                $this->machine = $machine;
        }

        /**
         * @return Player
         */
        public function getPlayer(): Player{
                return $this->player;
        }

        /**
         * @return int
         */
        public function getMachine(): int{
                return $this->machine;
        }

        /**
         * @param int $machine
         */
        public function setMachine(int $machine): void{
                $this->machine = $machine;
        }
}