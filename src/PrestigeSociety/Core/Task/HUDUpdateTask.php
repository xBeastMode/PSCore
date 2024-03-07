<?php

namespace PrestigeSociety\Core\Task;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
class HUDUpdateTask extends Task{

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * HUDUpdateTask constructor.
         * 
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        public function onRun(): void{
                $this->core->module_loader->hud->broadcastHUD();
        }

}