<?php
namespace PrestigeSociety\Events\Events\Casino;
use pocketmine\player\Player;
use PrestigeSociety\Events\CoreEvent;
class CasinoEndSpinEvent extends CoreEvent{
        /** @var Player */
        protected Player $player;
        /** @var int */
        protected int $machine;
        /** @var bool */
        protected bool $won;

        /**
         * CasinoEndSpinEvent constructor.
         *
         * @param Player $player
         * @param int    $machine
         * @param bool   $won
         */
        public function __construct(Player $player, int $machine, bool $won){
                $this->player = $player;
                $this->machine = $machine;
                $this->won = $won;
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

        /**
         * @return bool
         */
        public function isWon(): bool{
                return $this->won;
        }
}