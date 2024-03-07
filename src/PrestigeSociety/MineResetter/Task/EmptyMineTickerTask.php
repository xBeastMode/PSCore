<?php

namespace PrestigeSociety\MineResetter\Task;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
class EmptyMineTickerTask extends Task{

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;
        /** @var string|null */
        protected ?string $area;

        /**
         * EmptyMineTickerTask constructor.
         *
         * @param PrestigeSocietyCore $core
         * @param string|null         $area
         */
        public function __construct(PrestigeSocietyCore $core, string $area = null){
                $this->core = $core;
                $this->area = $area;
        }

        /**
         * Actions to execute when run
         *
         * @return void
         */
        public function onRun(): void{
                $this->core->module_loader->mine_resetter->resetEmptyMines($this->area);
        }
}