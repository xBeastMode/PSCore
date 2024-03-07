<?php
namespace PrestigeSociety\Directions;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Directions\Entity\DirectionsEntity;
class DirectionsListener implements Listener{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;
        /**
         * DirectionsListener constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param EntityTeleportEvent $event
         */
        public function onEntityLevelChange(EntityTeleportEvent $event){
                $entity = $event->getEntity();
                $from = $event->getFrom();
                $to =  $event->getTo();

                if($entity instanceof Player && $from->getWorld() !== $to->getWorld()){
                        $this->core->module_loader->directions->stopDirections($entity);
                }
        }

        /**
         * @param PlayerQuitEvent $event
         */
        public function onPlayerQuit(PlayerQuitEvent $event){
                $this->core->module_loader->directions->stopDirections($event->getPlayer());
        }

        /**
         * @param EntityDamageEvent $event
         */
        public function onEntityDamage(EntityDamageEvent $event){
                $entity = $event->getEntity();
                if($entity instanceof DirectionsEntity){
                        $event->cancel();
                }
        }

        /**
         * @param PlayerInteractEvent $event
         */
        public function onPlayerInteract(PlayerInteractEvent $event){
                $player = $event->getPlayer();
                $item = $event->getItem();
                $action = $event->getAction();

                $compass = $this->core->module_loader->directions->getCompass($player);

                if($compass !== null && $item->equalsExact($compass) && $action === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
                        $this->core->module_loader->directions->stopDirections($player);
                }
        }
}