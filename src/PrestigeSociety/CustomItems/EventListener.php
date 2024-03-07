<?php
namespace PrestigeSociety\CustomItems;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use PrestigeSociety\Core\PrestigeSocietyCore;
class EventListener implements Listener{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * CustomItems constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param PlayerInteractEvent $event
         */
        public function onPlayerInteract(PlayerInteractEvent $event){
                $player = $event->getPlayer();
                $item = $event->getItem();

                $action = $event->getAction();
                if($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
                        if($this->core->module_loader->custom_items->onUse($player, $item)){
                                $event->cancel();
                        }
                }
        }
}