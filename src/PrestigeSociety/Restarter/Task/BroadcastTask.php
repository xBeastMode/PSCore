<?php
namespace PrestigeSociety\Restarter\Task;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\Utils\ServerUtils;
class BroadcastTask extends Task{

        /** @var PrestigeSocietyCore */
        private PrestigeSocietyCore $core;

        /**
         * BroadcastTask constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        public function onRun(): void{
                if($this->core->module_loader->restarter->getTime() > 10){
                        $message = $this->core->getMessage("restarter", "time_message");

                        $message = RandomUtils::colorMessage($message);
                        $message = RandomUtils::restarterTextReplacer($message);

                        ServerUtils::broadcastMessage($message);
                }
        }
}