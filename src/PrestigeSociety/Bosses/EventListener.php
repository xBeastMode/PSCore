<?php
namespace PrestigeSociety\Bosses;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\Listener;
use pocketmine\event\world\ChunkUnloadEvent;
use PrestigeSociety\Bosses\Entity\BossEntity;
use PrestigeSociety\Bosses\Entity\BossTimer;
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
         * @param EntityDeathEvent $event
         *
         * @throws \JsonException
         */
        public function entityDeath(EntityDeathEvent $event){
                $entity = $event->getEntity();

                if($entity instanceof BossEntity && $entity->respawns){
                        $this->core->module_loader->bosses->scheduleRespawn($this->core->module_loader->bosses->getNextBoss());
                }
        }

        /**
         * @param EntityDamageEvent $event
         */
        public function onEntityDamage(EntityDamageEvent $event){
                $entity = $event->getEntity();

                if($entity instanceof BossTimer){
                        $event->cancel();
                }
        }

        /**
         * @param ChunkUnloadEvent $event
         */
        public function chunkUnload(ChunkUnloadEvent $event){
                $entities = $event->getWorld()->getChunkEntities($event->getChunkX(), $event->getChunkZ());

                foreach($entities as $entity){
                        if($entity instanceof BossEntity || $entity instanceof BossTimer){
                                $event->cancel();
                                return;
                        }
                }
        }

        /**
         * @param EntityMotionEvent $event
         */
        public function entityMotion(EntityMotionEvent $event){
                $entity = $event->getEntity();

                if($entity instanceof BossTimer){
                        $event->cancel();
                }
        }
}