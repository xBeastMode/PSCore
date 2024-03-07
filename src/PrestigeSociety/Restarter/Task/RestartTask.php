<?php
namespace PrestigeSociety\Restarter\Task;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\Utils\ServerUtils;
class RestartTask extends Task{

        /** @var PrestigeSocietyCore */
        private PrestigeSocietyCore $core;

        /**
         * RestartTask constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        public function onRun(): void{
                $this->core->module_loader->restarter->subtractTime(1);
                if($this->core->module_loader->restarter->getTime() <= 0){
                        foreach(ServerUtils::getOnlinePlayers() as $player){
                                if($this->core->module_loader->fun_box->isLSDEnabled($player)){
                                        $this->core->module_loader->fun_box->toggleLSD($player);
                                }
                                if($this->core->module_loader->combat_logger->inCombat($player)){
                                        $this->core->module_loader->combat_logger->endTime($player);
                                }
                        }
                        ServerUtils::transferAndShutDown();
                }
                if($this->core->module_loader->restarter->getTime() < 10){
                        $message = $this->core->getMessage("restarter", "count_down_message");

                        $message = RandomUtils::colorMessage($message);
                        $message = RandomUtils::restarterTextReplacer($message);

                        ServerUtils::broadcastMessage($message);
                }
        }
}