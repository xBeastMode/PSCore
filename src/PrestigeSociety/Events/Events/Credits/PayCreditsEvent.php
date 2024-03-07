<?php
namespace PrestigeSociety\Events\Events\Credits;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use PrestigeSociety\Events\CoreEvent;
class PayCreditsEvent extends CoreEvent implements Cancellable{
        use CancellableTrait;

        /** @var Player|string */
        protected string|Player $player;
        /** @var Player|string */
        protected string|Player $target;
        /** @var int */
        protected int $credits;

        /**
         * PayCreditsEvent constructor.
         *
         * @param string|Player $player
         * @param string|Player $target
         * @param int           $credits
         */
        public function __construct(Player|string $player, Player|string $target, int $credits){
                $this->player = $player;
                $this->target = $target;
                $this->credits = $credits;
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
        public function getCredits(): int{
                return $this->credits;
        }

        /**
         * @param int $credits
         */
        public function setCredits(int $credits): void{
                $this->credits = $credits;
        }
}