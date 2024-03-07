<?php
namespace PrestigeSociety\Statistics;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use PrestigeSociety\Core\Particles\RainbowParticle;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Task\ParticleCircleTask;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Statistics\Entity\StatHuman;
class StatisticsListener implements Listener{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var bool[] */
        public static array $sessions = [];

        /**
         *
         * @param PrestigeSocietyCore $core
         *
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param EntitySpawnEvent $event
         */
        public function entitySpawn(EntitySpawnEvent $event){
                $entity = $event->getEntity();

                if($entity instanceof StatHuman){
                        $this->core->module_loader->statistics->load($entity);
                }
        }

        /**
         * @param EntityDamageEvent $event
         */
        public function onEntityDamage(EntityDamageEvent $event){
                $entity = $event->getEntity();

                if($event instanceof EntityDamageByEntityEvent){
                        if(!$entity instanceof StatHuman){
                                return;
                        }

                        $event->cancel();

                        $damager = $event->getDamager();
                        if($damager instanceof Player){
                                if(isset(self::$sessions[spl_object_hash($damager)])){
                                        $entity->close();

                                        $damager->sendMessage(RandomUtils::colorMessage("&aEntity removed."));
                                        unset(self::$sessions[spl_object_hash($damager)]);

                                        return;
                                }

                                if($entity->statsProfile !== ""){
                                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->player_data->PROFILE_ID, $damager, $entity->statsProfile);
                                }
                        }
                }
        }
}