<?php
namespace PrestigeSociety\Portals;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use pocketmine\world\World;
use PrestigeSociety\Core\EventListener;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\ConsoleUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Portals\Task\PortalCheckTask;
class Portals{
        const QUEUE_SECONDS = 10;

        /** @var array */
        protected array $loaded_portals = [];
        /** @var array */
        protected array $queue = [];
        /** @var array */
        protected array $in_portal_ticks = [];
        /** @var array */
        protected array $name_queue = [];
        /** @var bool */
        protected bool $task_running = false;

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * Portals constructor.
         * 
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }
        
        public function reloadPortals(){
                $this->loaded_portals = $this->core->module_loader->land_protector->getAreasWithData("portal");

                if(!$this->task_running){
                        $this->core->getScheduler()->scheduleRepeatingTask(new PortalCheckTask($this->core), 1);
                        $this->task_running = true;
                }
        }

        /**
         * @param string $name
         */
        public function loadPortal(string $name){
                if($this->core->module_loader->land_protector->areaHasExtraData($name, "portal")){
                        if(PermissionManager::getInstance()->getPermission("portal.$name") === null){
                                PermissionManager::getInstance()->addPermission(new Permission("portal.$name", ""));
                        }
                        $this->loaded_portals[$name] = $this->core->module_loader->land_protector->getAreaData($name);
                }
        }

        /**
         *
         * @param string $name
         *
         */
        public function unloadPortal(string $name){
                if(isset($this->loaded_portals[$name])){
                        unset($this->loaded_portals[$name]);
                }
        }

        /**
         * @return Entity[]
         */
        public function getEntitiesInPortals(): array{
                $entities = [];

                foreach($this->loaded_portals as $name => $areaData){
                        $minX = min($areaData["min"][0], $areaData["max"][0]);
                        $minY = min($areaData["min"][1], $areaData["max"][1]);
                        $minZ = min($areaData["min"][2], $areaData["max"][2]);
                        $maxX = max($areaData["min"][0], $areaData["max"][0]);
                        $maxY = max($areaData["min"][1], $areaData["max"][1]);
                        $maxZ = max($areaData["min"][2], $areaData["max"][2]);

                        if(!$this->core->getServer()->getWorldManager()->isWorldLoaded($areaData["world"])){
                                $this->core->getServer()->getWorldManager()->loadWorld($areaData["world"]);
                        }

                        $level = $this->core->getServer()->getWorldManager()->getWorldByName($areaData["world"]);
                        if($level instanceof World){
                                foreach($level->getNearbyEntities(new AxisAlignedBB($minX, $minY, $minZ, $maxX, $maxY, $maxZ)) as $entity){
                                        $entities[$entity->getId()] = [$name, $entity];
                                }
                        }
                }

                return $entities;
        }

        /**
         * @param string $name
         *
         * @return Vector3|null
         */
        #[Pure] public function getPortalVector(string $name): ?Vector3{
                $areaData = $this->loaded_portals[$name] ?? null;
                if($areaData !== null){
                        $minX = min($areaData["min"][0], $areaData["max"][0]);
                        $minY = min($areaData["min"][1], $areaData["max"][1]);
                        $minZ = min($areaData["min"][2], $areaData["max"][2]);
                        $maxX = max($areaData["min"][0], $areaData["max"][0]);
                        $maxY = max($areaData["min"][1], $areaData["max"][1]);
                        $maxZ = max($areaData["min"][2], $areaData["max"][2]);

                        return new Vector3($minX + (round(($maxX - $minX) / 2)), $minY + (round(($maxY - $minY) / 2)), $minZ + (round(($maxZ - $minZ) / 2)));
                }
                return null;
        }

        public function runCheck(): void{
                $entities = $this->getEntitiesInPortals();

                foreach($this->queue as $id => $entity){
                        if(!$entity->isAlive()) continue;

                        /** @var Entity $entity */
                        $name = $this->name_queue[$id];

                        $check_combat = $this->loaded_portals[$name]["extra_data"]["check_combat"] ?? false;
                        $check_combat = RandomUtils::toBool($check_combat);

                        if(!isset($entities[$id])){
                                if($entity instanceof Player && !$entity->isClosed() && !$entity->hasPermission("pl.cl.admin")){
                                        if($check_combat && $this->core->module_loader->combat_logger->inCombat($entity)){
                                                $entity_location = $entity->getLocation();
                                                $position = $this->getPortalVector($name);

                                                $deltaX = $entity_location->x - $position->x;
                                                $deltaZ = $entity_location->z - $position->z;

                                                RandomUtils::knockBack($entity, -$deltaX, -$deltaZ, 0.5);

                                                $entity->sendTip(RandomUtils::colorMessage($this->loaded_portals[$name]["extra_data"]["check_combat_message"]));
                                                continue;
                                        }
                                }

                                unset($this->queue[$id], $this->name_queue[$id]);
                        }else{
                                $armorCheck = $this->loaded_portals[$name]["extra_data"]["armor_check"] ?? [];
                                if(!empty($armorCheck)){
                                        /** @var Player|Entity $entity */

                                        /** @var Item[] $armorInventory */
                                        $armorInventory = $entity->getArmorInventory()->getContents(true);

                                        if(RandomUtils::toBool($armorCheck["check_enchantments"])){
                                                foreach($armorInventory as $item){
                                                        if(count($item->getEnchantments()) > 0){
                                                                if(!isset($this->in_portal_ticks[$entity->getId()])){
                                                                        $this->in_portal_ticks[$entity->getId()] = 0;
                                                                }
                                                                $this->in_portal_ticks[$entity->getId()]++;
                                                                $ticks = $this->in_portal_ticks[$entity->getId()];

                                                                $actionDelay = (int) $armorCheck["action_delay"];

                                                                if($ticks === $actionDelay){
                                                                        $entity->setHealth(0);
                                                                }elseif($ticks % 20 === 0){
                                                                        $entity->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 20, 10));

                                                                        $entity->setTitleDuration(0, 10, 0);
                                                                        $entity->sendTitle($armorCheck["title"], str_replace("@seconds", round(($actionDelay - $ticks) / 20, 0), $armorCheck["subtitle"]));
                                                                }

                                                                continue 2;
                                                        }
                                                }
                                        }

                                        if($armorInventory[0]->getId() !== $armorCheck["armor"][0]
                                            || $armorInventory[1]->getId() !== $armorCheck["armor"][1]
                                            || $armorInventory[2]->getId() !== $armorCheck["armor"][2]
                                            || $armorInventory[3]->getId() !== $armorCheck["armor"][3]){
                                                if(!isset($this->in_portal_ticks[$entity->getId()])){
                                                        $this->in_portal_ticks[$entity->getId()] = 0;
                                                }
                                                $this->in_portal_ticks[$entity->getId()]++;
                                                $ticks = $this->in_portal_ticks[$entity->getId()];

                                                $actionDelay = (int) $armorCheck["action_delay"];

                                                if($ticks === $actionDelay){
                                                        $entity->setHealth(0);
                                                }elseif($ticks % 20 === 0){
                                                        $entity->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 20, 10));

                                                        $entity->setTitleDuration(0, 10, 0);
                                                        $entity->sendTitle($armorCheck["title"], str_replace("@seconds", round(($actionDelay - $ticks) / 20, 0), $armorCheck["subtitle"]));
                                                }
                                                continue;
                                        }
                                }

                                if($check_combat){
                                        $cause = $entity->getLastDamageCause();

                                        if($cause instanceof EntityDamageByEntityEvent && $cause->getDamager() instanceof Player){

                                                /** @var Player $damager */
                                                $damager = $cause->getDamager();
                                                $time = EventListener::$damage_times[$id] ?? time();

                                                if(!$this->core->module_loader->combat_logger->inCombat($damager)) continue;

                                                if(!isset($entities[$damager->getId()])
                                                    && $damager instanceof Player
                                                    && ((time() - $time) <= 1)){
                                                        $damager->teleport($entity->getLocation());

                                                        $message = $this->loaded_portals[$name]["extra_data"]["check_combat_message_damager"];
                                                        $message = str_replace("@target", $entity->getName(), $message);

                                                        $damager->sendMessage(RandomUtils::colorMessage($message));
                                                }
                                        }
                                }

                                unset($this->in_portal_ticks[$entity->getId()]);
                        }
                }

                foreach($this->in_portal_ticks as $id => $entity){
                        if(!isset($entities[$id])) unset($this->in_portal_ticks[$id]);
                }

                foreach($entities as $id => $data){
                        $name = $data[0];
                        $entity = $data[1];

                        if($entity instanceof Player){
                                if(isset($this->queue[$entity->getId()]) || !$entity->isAlive()) continue;

                                $data = $this->loaded_portals[$name]["extra_data"]["restricted_area"] ?? [];

                                if(!$entity->hasPermission("portal.$name")){
                                        if(!empty($data)){
                                                if(!isset($this->in_portal_ticks[$entity->getId()])){
                                                        $this->in_portal_ticks[$entity->getId()] = 0;
                                                }
                                                $this->in_portal_ticks[$entity->getId()]++;
                                                $ticks = $this->in_portal_ticks[$entity->getId()];

                                                $actionDelay = (int)$data["action_delay"];
                                                if($ticks === $actionDelay){
                                                        $entity->setHealth(0);
                                                }elseif($ticks % 20 === 0){
                                                        $entity->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 20, 10));

                                                        $entity->setTitleDuration(0, 10, 0);
                                                        $entity->sendTitle($data["title"], str_replace("@seconds", round(($actionDelay - $ticks) / 20, 0), $data["subtitle"]));
                                                }
                                        }else{

                                                if(!isset($this->in_portal_ticks[$entity->getId()])){
                                                        $this->in_portal_ticks[$entity->getId()] = 0;
                                                        $entity->sendMessage(RandomUtils::colorMessage($this->core->getMessage("portals", "no_permission")));
                                                }
                                                $this->in_portal_ticks[$entity->getId()]++;
                                                $ticks = $this->in_portal_ticks[$entity->getId()];

                                                $entity_location = $entity->getLocation();
                                                $position = $this->getPortalVector($name);

                                                $deltaX = $entity_location->x - $position->x;
                                                $deltaZ = $entity_location->z - $position->z;

                                                RandomUtils::knockBack($entity, $deltaX, $deltaZ, 0.5);

                                                if($ticks % 20 === 0){
                                                        $entity->sendMessage(RandomUtils::colorMessage($this->core->getMessage("portals", "no_permission")));
                                                }
                                        }
                                        continue;
                                }

                                $armorCheck = $this->loaded_portals[$name]["extra_data"]["armor_check"] ?? [];
                                if(!empty($armorCheck)){
                                        $armorInventory = $entity->getArmorInventory()->getContents(true);

                                        if(RandomUtils::toBool($armorCheck["check_enchantments"])){
                                                foreach($armorInventory as $item){
                                                        if(count($item->getEnchantments()) > 0){
                                                                $entity_location = $entity->getLocation();
                                                                $position = $this->getPortalVector($name);

                                                                $deltaX = $entity_location->x - $position->x;
                                                                $deltaZ = $entity_location->z - $position->z;

                                                                RandomUtils::knockBack($entity, $deltaX, $deltaZ, 0.5);

                                                                $entity->sendTip(RandomUtils::colorMessage($armorCheck["non_enchanted"]));
                                                                continue 2;
                                                        }
                                                }
                                        }

                                        if($armorInventory[0]->getId() !== $armorCheck["armor"][0]
                                            || $armorInventory[1]->getId() !== $armorCheck["armor"][1]
                                            || $armorInventory[2]->getId() !== $armorCheck["armor"][2]
                                            || $armorInventory[3]->getId() !== $armorCheck["armor"][3]){
                                                $entity_location = $entity->getLocation();
                                                $position = $this->getPortalVector($name);

                                                $deltaX = $entity_location->x - $position->x;
                                                $deltaZ = $entity_location->z - $position->z;

                                                RandomUtils::knockBack($entity, $deltaX, $deltaZ, 0.5);

                                                $entity->sendTip(RandomUtils::colorMessage($armorCheck["required_gear"]));
                                                continue;
                                        }
                                }

                                $commands = $this->loaded_portals[$name]["extra_data"]["commands"];
                                $rank = $this->core->module_loader->ranks->getRank($entity);

                                foreach($commands["p"] as $command){
                                        $command = str_replace(["@player", "@rank"], [$entity->getName(), $rank], $command);
                                        $this->core->getServer()->dispatchCommand($entity, $command, true);
                                }

                                foreach($commands["c"] as $command){
                                        $command = str_replace(["@player", "@rank"], [$entity->getName(), $rank], $command);
                                        ConsoleUtils::dispatchCommandAsConsole($command);
                                }

                                $this->queue[$entity->getId()] = $entity;
                                $this->name_queue[$entity->getId()] = $name;
                        }
                }
        }
}