<?php
namespace PrestigeSociety\Worlds;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class WorldsListener implements Listener{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * WorldsListener constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param PlayerJoinEvent $event
         */
        public function onPlayerJoin(PlayerJoinEvent $event){
                if($this->core->module_loader->worlds->isPerWorldInventoryEnabled()){
                        $player = $event->getPlayer();
                        $world = $event->getPlayer()->getWorld()->getDisplayName();

                        switch($this->core->module_loader->worlds->getTargetInventoryType($world)){
                                case "@saved":
                                        $this->core->module_loader->worlds->getWorldInventory()->equipInventory($player, $world);
                                        break;
                                case "@clear":
                                        $player->getArmorInventory()->clearAll();
                                        $player->getInventory()->clearAll();
                                        break;
                                case "@linked":
                                        $this->core->module_loader->worlds->getWorldInventory()->equipInventory($player, "@linked");
                                        break;
                        }
                }
        }

        public function onEntityLevelChange(EntityTeleportEvent $event){
                if($this->core->module_loader->worlds->isPerWorldInventoryEnabled()){
                        $player = $event->getEntity();
                        if($player instanceof Player){
                                $origin = $event->getFrom()->getWorld()->getDisplayName();
                                $world = $event->getTo()->getWorld()->getDisplayName();

                                if($origin !== $world){
                                        $target = $this->core->module_loader->worlds->getTargetInventoryType($origin);

                                        if($target === "@saved"){
                                                $this->core->module_loader->worlds->getWorldInventory()->savePlayerInventory($player, $origin);
                                        }elseif($target === "@linked"){
                                                $this->core->module_loader->worlds->getWorldInventory()->savePlayerInventory($player, "@linked");
                                        }

                                        switch($this->core->module_loader->worlds->getTargetInventoryType($world)){
                                                case "@saved":
                                                        $this->core->module_loader->worlds->getWorldInventory()->equipInventory($player, $world);
                                                        break;
                                                case "@clear":
                                                        $player->getArmorInventory()->clearAll();
                                                        $player->getInventory()->clearAll();
                                                        break;
                                                case "@linked":
                                                        $this->core->module_loader->worlds->getWorldInventory()->equipInventory($player, "@linked");
                                                        break;
                                        }
                                }
                        }
                }
        }

        /**
         * @param PlayerQuitEvent $event
         */
        public function onPlayerQuit(PlayerQuitEvent $event){
                if($this->core->module_loader->worlds->isPerWorldInventoryEnabled()){
                        $player = $event->getPlayer();
                        $world = $event->getPlayer()->getWorld()->getDisplayName();

                        $target = $this->core->module_loader->worlds->getTargetInventoryType($world);

                        if($target === "@saved"){
                                $this->core->module_loader->worlds->getWorldInventory()->savePlayerInventory($player, $world);
                        }elseif($target === "@linked"){
                                $this->core->module_loader->worlds->getWorldInventory()->savePlayerInventory($player, "@linked");
                        }
                }
        }

        /**
         * @param CommandEvent $event
         */
        public function onCommand(CommandEvent $event){
                $command = $event->getCommand();
                $command = explode(" ", $command);

                $sender = $event->getSender();
                if($sender instanceof Player){
                        $world = $sender->getWorld()->getDisplayName();
                        if($this->core->module_loader->worlds->isCommandBlocked($command[0], $world)){
                                $message = $this->core->getMessage("worlds", "command_blocked");
                                $message = str_replace(["@world", "@command"], [$command[0], $world], $message);
                                $sender->sendMessage(RandomUtils::colorMessage($message));
                                $event->cancel();
                        }
                }
        }

        /**
         * @param WorldLoadEvent $event
         */
        public function onWorldLoad(WorldLoadEvent $event){
                $world = $event->getWorld();
                $world_time = $this->core->module_loader->worlds->getWorldTime($world->getDisplayName());

                if($world_time !== null){
                        $world->setTime($world_time);
                        $world->stopTime();
                }
        }
}