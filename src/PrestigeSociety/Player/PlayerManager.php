<?php
namespace PrestigeSociety\Player;
use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
class PlayerManager{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;
        /** @var Player */
        protected Player $player;

        /**
         * PlayerManager constructor.
         *
         * @param PrestigeSocietyCore $core
         * @param Player              $player
         */
        public function __construct(PrestigeSocietyCore $core, Player $player){
                $this->core = $core;
                $this->player = $player;
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
        public function isRegistered(): bool{
                return $this->core->module_loader->levels->playerExists($this->player);
        }

        /**
         * @return bool
         */
        public function isSpinningSlotMachine(): bool{
                return $this->core->module_loader->casino->isSpinning($this->player);
        }

        /**
         * @param bool $won
         */
        public function stopSlotMachineSpin(bool $won){
                $this->core->module_loader->casino->endSpin($this->player, $won);
        }

        /**
         * @return bool
         */
        #[Pure] public function isMuted(): bool{
                return $this->core->module_loader->chat->isMuted($this->player);
        }

        /**
         * @return bool
         */
        #[Pure] public function getMuteSeconds(): bool{
                return $this->core->module_loader->chat->getMuteSeconds($this->player);
        }

        /**
         * @return string
         */
        #[Pure] public function getMuteReason(): string{
                return $this->core->module_loader->chat->getMuteReason($this->player);
        }

        /**
         * @param int    $seconds
         * @param string $reason
         */
        public function mute(int &$seconds = 60, string &$reason = ""){
                $this->core->module_loader->chat->mutePlayer($this->player, $seconds, $reason);
        }

        /**
         * @return bool
         */
        public function unMute(): bool{
                return $this->core->module_loader->chat->unMutePlayer($this->player);
        }

        public function formatDisplayName(): void{
                $this->player->setDisplayName($this->core->module_loader->chat->formatDisplayName($this->player));
        }
}