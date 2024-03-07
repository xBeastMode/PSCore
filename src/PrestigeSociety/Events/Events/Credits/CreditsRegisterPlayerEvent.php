<?php
namespace PrestigeSociety\Events\Events\Credits;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use PrestigeSociety\Events\CoreEvent;
class CreditsRegisterPlayerEvent extends CoreEvent implements Cancellable{
        use CancellableTrait;

        /** @var Player|string */
        protected string|Player $player;

        /**
         * CreditsRegisterPlayerEvent constructor.
         *
         * @param string|Player $player
         */
        public function __construct(Player|string $player){
                $this->player = $player;
        }

        /**
         * @return Player|string
         */
        public function getPlayer(): Player|string{
                return $this->player;
        }
}