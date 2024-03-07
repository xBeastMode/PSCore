<?php
namespace PrestigeSociety\CombatLogger;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
class CombatLoggerListener implements Listener{
        /** @var PrestigeSocietyCore */
        private PrestigeSocietyCore $core;

        /** @var array|bool|mixed */
        protected mixed $allowed_commands = [];

        /**
         * CombatLoggerListener constructor.
         * 
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
                $this->allowed_commands = $core->getConfig()->get("ct_allowed_commands");
        }

        /**
         * @priority MONITOR
         *
         * @param PlayerQuitEvent $event
         */
        public function onPlayerQuit(PlayerQuitEvent $event){
                $player = $event->getPlayer();

                if($this->core->module_loader->combat_logger->inCombat($player)){
                        $player->kill();
                        $this->core->module_loader->combat_logger->endTime($player);
                }
        }

        /**
         * @priority MONITOR
         *
         * @param PlayerDeathEvent $event
         */
        public function onPlayerDeath(PlayerDeathEvent $event){
                $this->core->module_loader->combat_logger->endTime($event->getPlayer());
        }

        /**
         * @priority LOWEST
         *
         * @param PlayerCommandPreprocessEvent $event
         *
         * @throws \InvalidStateException
         */
        public function onPlayerCommandPreprocess(PlayerCommandPreprocessEvent $event){
                if($event->getMessage()[0] !== "/" || in_array($event->getMessage(), $this->allowed_commands)){
                        return;
                }

                if($this->core->module_loader->combat_logger->inCombat($event->getPlayer()) and !$event->getPlayer()->hasPermission("combatlog.admin")){
                        $event->getPlayer()->sendMessage(RandomUtils::colorMessage($this->core->getMessage("combat_logger", "no_commands")));
                        $event->cancel();
                }
        }

        /**
         * @priority HIGHEST
         *
         * @param EntityDamageEvent $event
         */
        public function onEntityDamage(EntityDamageEvent $event){
                if($event instanceof EntityDamageByEntityEvent){
                        $player = $event->getEntity();
                        $damager = $event->getDamager();

                        if($player instanceof Player){
                                if(!$this->core->module_loader->combat_logger->inCombat($player) && $this->core->module_loader->fun_box->isGodEnabled($player)){
                                        $event->cancel();
                                }
                        }

                        if($damager instanceof Player and $player instanceof Player){
                                if(!$this->core->module_loader->land_protector->canDamageWold($player->getWorld()->getDisplayName()) || !$this->core->module_loader->land_protector->canDamage($player->getPosition())) return;

                                if($event->isCancelled()) return;

                                $this->core->module_loader->fun_box->disableLSD($damager);
                                $this->core->module_loader->fun_box->disableGod($damager);
                                $this->core->module_loader->fun_box->disableFlight($damager);

                                $combatTime = $this->core->getConfig()->getAll()["combat_logger"]["time"];

                                $this->core->module_loader->combat_logger->checkTime($player, $combatTime);
                                $this->core->module_loader->combat_logger->checkTime($damager, $combatTime);
                        }
                }
        }
}