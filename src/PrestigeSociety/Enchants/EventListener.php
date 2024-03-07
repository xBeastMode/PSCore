<?php
namespace PrestigeSociety\Enchants;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use PrestigeSociety\Core\PrestigeSocietyCore;
class EventListener implements Listener{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * EventListener constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param BlockBreakEvent $event
         *
         * @priority HIGHEST
         */
        public function onBlockBreak(BlockBreakEvent $event){
                $player = $event->getPlayer();
                $block = $event->getBlock();

                if($this->core->module_loader->land_protector->isInMine($block) && $player->getInventory()->firstEmpty() !== -1 && !$event->isCancelled()){
                        $this->core->module_loader->enchants->getShardRandom($player);
                }
        }

}