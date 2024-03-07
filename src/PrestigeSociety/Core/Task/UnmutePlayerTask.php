<?php
namespace PrestigeSociety\Core\Task;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class UnmutePlayerTask extends Task{
        /** @var Player */
        protected Player $player;
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * UnmutePlayerTask constructor.
         *
         * @param PrestigeSocietyCore $core
         * @param Player              $player
         */
        public function __construct(PrestigeSocietyCore $core, Player $player){
                $this->core = $core;
                $this->player = $player;
        }

        public function onRun(): void{
                $this->core->module_loader->chat->unMutePlayer($this->player);

                $msg = RandomUtils::colorMessage($this->core->getMessage("chat_protector", "unmuted"));
                $this->player->sendMessage($msg);
        }
}