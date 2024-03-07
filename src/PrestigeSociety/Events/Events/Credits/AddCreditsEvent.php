<?php
namespace PrestigeSociety\Events\Events\Credits;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use PrestigeSociety\Events\CoreEvent;
class AddCreditsEvent extends CoreEvent implements Cancellable{
        use CancellableTrait;

        /** @var Player|string */
        protected string|Player $player;
        /** @var int */
        protected int $credits;

        /**
         * AddCreditsEvent constructor.
         *
         * @param string|Player $player
         * @param int           $credits
         */
        public function __construct(Player|string $player, int $credits){
                $this->player = $player;
                $this->credits = $credits;
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