<?php
namespace PrestigeSociety\Events\Events\Economy;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use PrestigeSociety\Events\CoreEvent;
class PayMoneyEvent extends CoreEvent implements Cancellable{
        use CancellableTrait;

        /** @var Player|string */
        protected string|Player $player;
        /** @var Player|string */
        protected string|Player $target;
        /** @var int */
        protected int $money;

        /**
         * PayMoneyEvent constructor.
         *
         * @param string|Player $player
         * @param string|Player $target
         * @param int           $money
         */
        public function __construct(Player|string $player, Player|string $target, int $money){
                $this->player = $player;
                $this->target = $target;
                $this->money = $money;
        }

        /**
         * @return Player|string
         */
        public function getPlayer(): Player|string{
                return $this->player;
        }

        /**
         * @return Player|string
         */
        public function getTarget(): Player|string{
                return $this->target;
        }

        /**
         * @return int
         */
        public function getMoney(): int{
                return $this->money;
        }

        /**
         * @param int $money
         */
        public function setMoney(int $money): void{
                $this->money = $money;
        }
}