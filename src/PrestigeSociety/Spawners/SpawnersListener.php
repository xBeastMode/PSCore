<?php
namespace PrestigeSociety\Spawners;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use PrestigeSociety\Core\PrestigeSocietyCore;
class SpawnersListener implements Listener{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * FormListener constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        public function onBlockPlace(BlockPlaceEvent $event){
        }
}