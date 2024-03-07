<?php
namespace PrestigeSociety\Events\Events\Economy;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use PrestigeSociety\Events\CoreEvent;
class SetMoneyEvent extends CoreEvent implements Cancellable{
        use CancellableTrait;

        /** @var Player|string */
        protected string|Player $player;
        /** @var int */
        protected int $money;

        /**
         * SetMoneyEvent constructor.
         *
         * @param string|Player $player
         * @param int           $money
         */
        public function __construct(Player|string $player, int $money){
                $this->player = $player;
                $this->money = $money;
        }

        /**
         * @return Player|string
         */
        public function getPlayer(): Player|string{
                return $this->player;
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