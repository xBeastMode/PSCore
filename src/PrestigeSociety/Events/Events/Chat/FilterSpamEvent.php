<?php
namespace PrestigeSociety\Events\Events\Chat;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use PrestigeSociety\Events\CoreEvent;
class FilterSpamEvent extends CoreEvent implements Cancellable{
        use CancellableTrait;

        /** @var Player */
        protected Player $player;
        /** @var int */
        protected int $cooldown;

        /**
         * FilterSpamEvent constructor.
         *
         * @param Player $player
         * @param int    $cooldown
         */
        public function __construct(Player $player, int $cooldown){
                $this->player = $player;
                $this->cooldown = $cooldown;
        }

        /**
         * @return Player
         */
        public function getPlayer(): Player{
                return $this->player;
        }

        /**
         * @return int
         */
        public function getCooldown(): int{
                return $this->cooldown;
        }

        /**
         * @param int $cooldown
         */
        public function setCooldown(int $cooldown): void{
                $this->cooldown = $cooldown;
        }
}