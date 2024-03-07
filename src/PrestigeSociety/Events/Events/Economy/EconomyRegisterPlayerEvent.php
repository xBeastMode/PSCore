<?php
namespace PrestigeSociety\Events\Events\Economy;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use PrestigeSociety\Events\CoreEvent;
class EconomyRegisterPlayerEvent extends CoreEvent implements Cancellable{
        use CancellableTrait;

        /** @var Player|string */
        protected string|Player $player;

        /**
         * EconomyRegisterPlayerEvent constructor.
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