<?php
namespace PrestigeSociety\MineResetter\Task;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
class MineTickerTask extends Task{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;
        /** @var int */
        protected int $resetTime = 0;

        /**
         * MineTickerTask constructor.
         *
         * @param PrestigeSocietyCore $core
         * @param int                 $resetTime
         */
        public function __construct(PrestigeSocietyCore $core, int $resetTime){
                $this->core = $core;
                $this->resetTime = $resetTime;
        }

        /**
         * Actions to execute when run
         *
         * @return void
         */
        public function onRun(): void{
                if($this->resetTime === 0){
                        $this->core->module_loader->mine_resetter->notifyRestart();
                }
                $this->resetTime--;
        }
}