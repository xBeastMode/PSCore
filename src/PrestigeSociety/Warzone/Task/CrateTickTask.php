<?php

namespace PrestigeSociety\Warzone\Task;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Warzone\Entity\LootCrate;
class CrateTickTask extends Task{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var LootCrate */
        protected LootCrate $crate;
        /** @var int */
        protected int $period = 0;

        /**
         * CrateTickTask constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        public function onRun(): void{
                $warzone = $this->core->module_loader->warzone;

                if(++$this->period === $warzone->getCrateRespawnPeriod()){
                        $warzone->respawnLootCrate();
                        $this->period = 0;
                }
        }
}