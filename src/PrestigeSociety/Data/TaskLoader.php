<?php
namespace PrestigeSociety\Data;
use PrestigeSociety\Chat\Task\BroadcasterTask;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Task\HUDUpdateTask;
use PrestigeSociety\Optimizer\OptimizeTask;
use PrestigeSociety\Restarter\Task\BroadcastTask;
use PrestigeSociety\Restarter\Task\RestartTask;
use PrestigeSociety\Warzone\Task\CrateTickTask;

class TaskLoader{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * ModuleLoader constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        public function loadTasks(){
                $config_data = $this->core->getConfig()->getAll();

                if($this->core->getConfig()->getAll()["broadcaster"]["enable"]){
                        $this->core->getScheduler()->scheduleRepeatingTask(new BroadcasterTask($this->core), intval($config_data["broadcaster"]["interval_seconds"]) * 20);
                }
                if($this->core->getConfig()->getAll()["restarter"]["enable"]){
                        $this->core->getScheduler()->scheduleRepeatingTask(new BroadcastTask($this->core), intval($config_data["restarter"]["broadcast_time"]) * 20);
                        $this->core->getScheduler()->scheduleRepeatingTask(new RestartTask($this->core), 20);
                }
                if($this->core->module_configurations->hud["enable"]){
                        $this->core->getScheduler()->scheduleRepeatingTask(new HUDUpdateTask($this->core), 5);
                }
                if($this->core->getConfig()->getAll()["optimizer"]["enable"]){
                        $this->core->getScheduler()->scheduleRepeatingTask(new OptimizeTask($this->core), 20);
                }

                $this->core->getScheduler()->scheduleRepeatingTask(new CrateTickTask($this->core), 20);
        }
}