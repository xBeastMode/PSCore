<?php
namespace PrestigeSociety\Recovery;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use PrestigeSociety\Core\PrestigeSocietyCore;
class RecoveryListener implements Listener{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * RecoveryListener constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param PlayerDeathEvent $event
         */
        public function onPlayerDeath(PlayerDeathEvent $event){
                $this->core->module_loader->recovery->backupItems($event->getPlayer(), $event->getDrops());
        }
}