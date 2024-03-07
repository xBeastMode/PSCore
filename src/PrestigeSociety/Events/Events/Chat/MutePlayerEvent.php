<?php
namespace PrestigeSociety\Events\Events\Chat;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use PrestigeSociety\Events\CoreEvent;
class MutePlayerEvent extends CoreEvent implements Cancellable{
        use CancellableTrait;

        /** @var Player */
        protected Player $player;
        /** @var int */
        protected int $time;
        /** @var string */
        protected string $reason;

        /**
         * MutePlayerEvent constructor.
         *
         * @param Player $player
         * @param int    $time
         * @param string $reason
         */
        public function __construct(Player $player, int $time, string $reason){
                $this->player = $player;
                $this->time = $time;
                $this->reason = $reason;
        }

        /**
         * @return Player
         */
        public function getPlayer(): Player{
                return $this->player;
        }

        /**
         * @param Player $player
         */
        public function setPlayer(Player $player): void{
                $this->player = $player;
        }

        /**
         * @return int
         */
        public function getTime(): int{
                return $this->time;
        }

        /**
         * @param int $time
         */
        public function setTime(int $time): void{
                $this->time = $time;
        }

        /**
         * @return string
         */
        public function getReason(): string{
                return $this->reason;
        }

        /**
         * @param string $reason
         */
        public function setReason(string $reason): void{
                $this->reason = $reason;
        }
}