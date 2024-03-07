<?php

namespace PrestigeSociety\Optimizer;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\Utils\ServerUtils;
class OptimizeTask extends Task{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var int */
        protected int $seconds = 0;
        /** @var int */
        protected int $period = 0;

        /**
         * OptimizeTask constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
                $this->seconds = $this->period = intval($this->core->getConfig()->getAll()["optimizer"]["optimizing_tick_seconds"]);
        }

        public function onRun(): void{
                --$this->seconds;

                $seconds = [30, 15, 10];

                if(in_array($this->seconds, $seconds) || $this->seconds <= 6){
                        $message = RandomUtils::colorMessage($this->core->getMessage("optimizer", "pre_warning"));
                        ServerUtils::broadcastMessage(str_replace("@seconds", $this->seconds, $message));
                }

                if($this->seconds <= 1){
                        Optimizer::clearLag();
                        $this->seconds = $this->period;

                        $message = RandomUtils::colorMessage($this->core->getMessage("optimizer", "post_warning"));
                        ServerUtils::broadcastMessage($message);
                }
        }
}