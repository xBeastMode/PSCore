<?php
namespace PrestigeSociety\Portals\Task;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
class PortalCheckTask extends Task{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * PortalCheckTask constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * Actions to execute when run
         *
         * @return void
         */
        public function onRun(): void{
                $this->core->module_loader->portals->runCheck();
        }
}