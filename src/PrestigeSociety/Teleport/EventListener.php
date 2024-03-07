<?php
namespace PrestigeSociety\Teleport;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\world\Position;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Teleport\Handle\TeleportQueue;
class EventListener implements Listener{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var int[] */
        public static array $time = [];

        /** @var Position[] */
        public static array $last_position = [];

        /**
         * EventListener constructor.
         *
         * @param PrestigeSocietyCore $base
         */
        public function __construct(PrestigeSocietyCore $base){
                $this->core = $base;
        }

        /**
         * @param EntityTeleportEvent $event
         */
        public function onEntityTeleport(EntityTeleportEvent $event){
                $position = $event->getFrom();
                $entity = $event->getEntity();

                if($entity instanceof Player){
                        self::$last_position[spl_object_hash($entity)] = $position;
                }
        }

        /**
         * @param PlayerMoveEvent $event
         */
        public function onPlayerMove(PlayerMoveEvent $event){
                $player = $event->getPlayer();
                /** @var Player[]|int[] $session */
                $session = TeleportQueue::getFromQueue($player);

                if($session !== null){

                        $to = $event->getTo();
                        $from = $event->getFrom();

                        if(!isset(self::$time[spl_object_hash($player)])){
                                self::$time[spl_object_hash($player)] = 0;
                        }

                        self::$time[spl_object_hash($player)]++;

                        if(($to->x !== $from->x || $to->z !== $from->z) && self::$time[spl_object_hash($player)] >= 10){
                                $message = $this->core->module_loader->teleport->getMessage("teleport_cancel");

                                $player->sendMessage(RandomUtils::colorMessage($message));
                                TeleportQueue::removeFromQueue($player);

                                unset(self::$time[spl_object_hash($player)]);
                        }
                }
        }

        /**
         * @param PlayerJoinEvent $event
         */
        public function onPlayerJoin(PlayerJoinEvent $event){
                $this->core->module_loader->teleport->teleport_api->pushIntoRequests($event->getPlayer());
        }

        /**
         * @param PlayerQuitEvent $event
         */
        public function onPlayerQuit(PlayerQuitEvent $event){
                $this->core->module_loader->teleport->teleport_api->removeFromRequests($event->getPlayer());
        }
}