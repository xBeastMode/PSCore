<?php
namespace PrestigeSociety\Events\Events\Chat;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use PrestigeSociety\Events\CoreEvent;
class UnMutedOfflinePlayerEvent extends CoreEvent implements Cancellable{
        use CancellableTrait;

        /** @var string */
        protected string $player;

        /**
         * UnMutedOfflinePlayerEvent constructor.
         *
         * @param string $player
         */
        public function __construct(string $player){
                $this->player = $player;
        }

        /**
         * @return string
         */
        public function getPlayer(): string {
                return $this->player;
        }
}