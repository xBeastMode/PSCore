<?php
namespace PrestigeSociety\Events\Events\Chat;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use PrestigeSociety\Events\CoreEvent;
class UnMutePlayerEvent extends CoreEvent implements Cancellable{
        use CancellableTrait;

        /** @var Player */
        protected Player $player;

        /**
         * UnMutePlayerEvent constructor.
         *
         * @param Player $player
         */
        public function __construct(Player $player){
                $this->player = $player;
        }

        /**
         * @return Player
         */
        public function getPlayer(): Player{
                return $this->player;
        }
}