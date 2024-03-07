<?php
namespace PrestigeSociety\Levels;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerDeathEvent;
use PrestigeSociety\Core\PrestigeSocietyCore;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
class LevelsListener implements Listener{
        /** @var PrestigeSocietyCore */
        private PrestigeSocietyCore $core;

        /**
         * LevelsListener constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->core = $plugin;
        }

        /**
         * @param PlayerJoinEvent $event
         *
         * @priority HIGHEST
         */
        public function onPlayerJoin(PlayerJoinEvent $event){
                $player = $event->getPlayer();

                if(!$this->core->module_loader->levels->playerExists($player)){
                        $this->core->module_loader->levels->addNewPlayer($player);
                }
        }

        /**
         * @param BlockBreakEvent $event
         *
         * @priority HIGHEST
         */
        public function onBlockBreak(BlockBreakEvent $event){
                if(!$event->isCancelled()){
                        $this->core->module_loader->levels->addTempBlockBreak($event->getPlayer());
                }
        }

        /**
         * @param BlockPlaceEvent $event
         */
        public function onBlockPlace(BlockPlaceEvent $event){
                $this->core->module_loader->levels->addTempBlockPlace($event->getPlayer());
        }


        /**
         * @param PlayerDeathEvent $event
         *
         * @priority HIGHEST
         */
        public function onPlayerDeath(PlayerDeathEvent $event){
                $target = $event->getPlayer();
                $cause = $event->getPlayer()->getLastDamageCause();

                if($cause instanceof EntityDamageByEntityEvent){

                        $killer = $cause->getDamager();
                        if(($killer instanceof Player) and ($target instanceof Player)){
                                $this->core->module_loader->levels->setKills($killer, $this->core->module_loader->levels->getKills($killer) + 1);
                                $this->core->module_loader->levels->setDeaths($target, $this->core->module_loader->levels->getDeaths($target) + 1);
                        }

                        return;
                }

                $this->core->module_loader->levels->setDeaths($target, $this->core->module_loader->levels->getDeaths($target) + 1);
        }
}