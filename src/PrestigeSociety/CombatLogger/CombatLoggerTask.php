<?php
namespace PrestigeSociety\CombatLogger;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\PrestigeSocietyCore;
class CombatLoggerTask extends Task{

        /** @var PrestigeSocietyCore */
        private PrestigeSocietyCore $core;

        /** @var Player */
        private Player $player;

        /**
         * CombatLoggerTask constructor.
         * 
         * @param PrestigeSocietyCore $core
         * @param Player              $player
         */
        public function __construct(PrestigeSocietyCore $core, Player $player){
                $this->core = $core;
                $this->player = $player;
        }

        public function onRun(): void{
                $this->core->module_loader->combat_logger->endTime($this->player);
                $this->player->sendMessage(RandomUtils::colorMessage($this->core->getMessage("combat_logger", "can_log_out")));
        }
}