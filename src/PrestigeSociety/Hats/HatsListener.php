<?php
namespace PrestigeSociety\Hats;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use PrestigeSociety\Core\PrestigeSocietyCore;
class HatsListener implements Listener{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * LandProtectorListener constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param PlayerQuitEvent $event
         */
        public function onPlayerQuit(PlayerQuitEvent $event){
                $player = $event->getPlayer();
                $this->core->module_loader->hats->removeHat($player);
        }
}