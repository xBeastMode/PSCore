<?php

namespace PrestigeSociety\MineResetter;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\MineResetter\Entity\ResetMineEntity;
class MineResetterListener implements Listener{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var bool[] */
        public static array $sessions = [];
        /** @var bool[] */
        public static array $cooldown = [];

        /**
         * DirectionsListener constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }


        /**
         * @param EntityDamageEvent $event
         */
        public function onEntityDamage(EntityDamageEvent $event){
                $entity = $event->getEntity();

                if(!$entity instanceof ResetMineEntity){
                        return;
                }

                $event->cancel();

                if($event instanceof EntityDamageByEntityEvent){
                        $damager = $event->getDamager();
                        if($damager instanceof Player){
                                if(isset(self::$sessions[spl_object_hash($damager)])){
                                        $entity->close();

                                        $damager->sendMessage(RandomUtils::colorMessage("&aEntity removed."));
                                        unset(self::$sessions[spl_object_hash($damager)]);

                                        return;
                                }

                                if(isset(self::$cooldown[spl_object_hash($entity)]) && (time() - self::$cooldown[spl_object_hash($entity)]) <= 30){
                                        $time = 30 - (time() - self::$cooldown[spl_object_hash($entity)]);
                                        $damager->sendMessage(RandomUtils::colorMessage("&l&8» &cPlease wait &4$time &csecond(s) to use again!"));

                                        return;
                                }

                                $mine = $entity->getWorld()->getDisplayName();
                                $this->core->module_loader->mine_resetter->resetEmptyMines($mine, function (string $area, bool $empty) use ($damager){
                                        if(!$empty){
                                                $damager->sendMessage(RandomUtils::colorMessage("&l&8» &cThat mine is not empty!"));
                                        }else{
                                                $this->core->module_loader->mine_resetter->notifyRestart($area);
                                        }
                                });

                                self::$cooldown[spl_object_hash($entity)] = time();
                        }
                }
        }
}