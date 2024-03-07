<?php
namespace PrestigeSociety\Events\Events\Fun;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use PrestigeSociety\Events\CoreEvent;
class ToggleFreezeEvent extends CoreEvent implements Cancellable{
        use CancellableTrait;

        /** @var Player */
        protected Player $sender;
        /** @var Player */
        protected Player $player;
        /** @var bool */
        protected bool $enabled;
        /** @var bool */
        protected bool $cancel_commands;

        /**
         * ToggleFreezeEvent constructor.
         *
         * @param Player $sender
         * @param Player $player
         * @param bool   $enabled
         * @param bool   $cancel_commands
         */
        public function __construct(Player $sender, Player $player, bool $enabled, bool $cancel_commands){
                $this->sender = $sender;
                $this->player = $player;
                $this->enabled = $enabled;
                $this->cancel_commands = $cancel_commands;
        }

        /**
         * @return Player
         */
        public function getSender(): Player{
                return $this->sender;
        }

        /**
         * @return Player
         */
        public function getPlayer(): Player{
                return $this->player;
        }

        /**
         * @return bool
         */
        public function isEnabled(): bool{
                return $this->enabled;
        }

        /**
         * @param bool $enabled
         */
        public function setEnabled(bool $enabled): void{
                $this->enabled = $enabled;
        }
        /**
         * @return bool
         */
        public function cancelCommands(): bool{
                return $this->cancel_commands;
        }

        /**
         * @param bool $cancel_commands
         */
        public function setCancelCommands(bool $cancel_commands): void{
                $this->cancel_commands = $cancel_commands;
        }
}