<?php
namespace PrestigeSociety\Bosses;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\ListTag;
use pocketmine\scheduler\TaskHandler;
use PrestigeSociety\Bosses\Entity\BossEntity;
use PrestigeSociety\Bosses\Entity\BossTimer;
use PrestigeSociety\Bosses\Task\BossRespawnTimer;
use PrestigeSociety\Bosses\Task\BossSpawnTask;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\Utils\SkinUtils;
use PrestigeSociety\Forms\FormList\Boss\BossResultForm;
use PrestigeSociety\Forms\FormManager;
class Bosses{
        /** @var int */
        protected static int $boss_id = 0;

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var TaskHandler[] */
        protected array $timers = [];
        /** @var TaskHandler[] */
        protected array $tasks = [];
        /** @var BossEntity[] */
        protected array $bosses = [];
        /** @var string[] */
        protected array $boss_names = [];

        /** @var int */
        public int $BOSS_RESULT_ID = 0;

        /**
         * Bosses constructor.
         *
         * @param PrestigeSocietyCore $core
         * @throws \JsonException
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                self::$boss_id = count($core->module_configurations->bosses);

                $this->core->getServer()->getPluginManager()->registerEvents(new EventListener($core), $core);
                $this->init();

                $this->BOSS_RESULT_ID = FormManager::getNextFormId();
                $this->core->module_loader->form_manager->registerHandler($this->BOSS_RESULT_ID, BossResultForm::class);
        }

        /**
         * @throws \JsonException
         */
        public function init(){
                $name = $this->getNextBoss();

                if($name !== null){
                        $boss = $this->core->module_configurations->bosses[$name];
                        $this->spawnBoss($name, RandomUtils::parseLocation($boss["position"]), $boss["size"], $boss["damage"], $boss["health"], $boss["custom"]);
                }
        }

        public function despawnAll(){
                foreach($this->core->getServer()->getWorldManager()->getWorlds() as $level){
                        foreach($level->getEntities() as $entity){
                                if($entity instanceof BossEntity || $entity instanceof BossTimer){
                                        $entity->onDespawn();
                                        $entity->close();
                                }
                        }
                }
        }

        /**
         * @return null|string
         */
        public function getNextBoss(): ?string{
                $config = $this->core->module_configurations->bosses;
                if(count($config) <= 0) return null;

                if(count($this->boss_names) <= 0){
                        $this->boss_names = array_keys($config);
                }

                return array_shift($this->boss_names);
        }

        /**
         * @param string $name
         * @param bool   $immediate
         * @param bool   $checkRespawn
         *
         * @return bool
         * @throws \JsonException
         */
        public function scheduleRespawn(string $name, bool $immediate = false, bool $checkRespawn = true): bool{
                if(!isset($this->core->module_configurations->bosses[$name])) return false;

                if(isset($this->tasks[$name])){
                        $this->tasks[$name]->cancel();
                }
                if(isset($this->timers[$name])){
                        $this->timers[$name]->cancel();
                }

                $boss_config = $this->core->module_configurations->bosses[$name];
                if($checkRespawn && !RandomUtils::toBool($boss_config["respawns"] ?? true)){
                        $this->scheduleRespawn($this->getNextBoss());
                        return false;
                }

                if(!$immediate){
                        $this->timers[$name] = $this->core->getScheduler()->scheduleRepeatingTask(new BossRespawnTimer($this->core, $name, RandomUtils::parseLocation($boss_config["position"]), $boss_config["respawn_period"]), 20);
                }
                $this->tasks[$name] = $this->core->getScheduler()->scheduleDelayedTask(new BossSpawnTask($this->core, $name, RandomUtils::parseLocation($boss_config["position"]), $boss_config["size"], $boss_config["damage"], $boss_config["health"], $boss_config["custom"]), $immediate ? 0 : intval($boss_config["respawn_period"]) * 20);

                return true;
        }

        /**
         * @param string   $name
         * @param Location $position
         * @param int      $size
         * @param int      $respawnPeriod
         * @param int      $damage
         * @param int      $health
         *
         * @throws \JsonException
         */
        public function addBossSpawn(string $name, Location $position, int $size, int $respawnPeriod, int $damage, int $health){
                $this->core->module_configurations->bosses[$name] = [
                    "position"       => [$position->x, $position->y, $position->z, $position->getWorld()->getDisplayName()],
                    "size"           => $size,
                    "hand_item"      => "286:0",
                    "damage"         => $damage,
                    "health"         => $health,
                    "respawn_period" => $respawnPeriod,
                    "commands"       => [],
                    "buffs"          => [],
                    "skin"           => "athena.png",
                ];
                $this->core->module_configurations->saveBossesConfig();
                $this->spawnBoss($name, $position, $size, $damage, $health);
        }


        /**
         * @param string $name
         *
         * @return bool
         */
        public function removeBoss(string $name): bool{
                if(!isset($this->core->module_configurations->bosses[$name])) return true;

                unset($this->core->module_configurations->bosses[$name]);
                $this->core->module_configurations->saveBossesConfig();

                $this->bosses[$name]->close();
                if(isset($this->tasks[$name])) $this->tasks[$name]->cancel();
                if(isset($this->timers[$name])) $this->timers[$name]->cancel();

                unset($this->tasks[$name], $this->bosses[$name], $this->timers[$name]);
                return false;
        }

        /**
         * @param string   $name
         * @param Location $location
         * @param int      $size
         * @param int      $damage
         * @param int      $health
         * @param bool     $custom
         *
         * @return bool
         * @throws \JsonException
         */
        public function spawnBoss(string $name, Location $location, int $size, int $damage, int $health, bool $custom = false): bool{
                $nbt = RandomUtils::generateSkinCompoundTag($skinData = $this->parseSkinData($name));

                $boss = $custom ? "PrestigeSociety\\Bosses\\Entity\\Custom\\$name\\$name" : "PrestigeSociety\\Bosses\\Entity\\BossEntity";
                $boss = new $boss($location, new Skin("Steve" . time(), $skinData), $nbt, $this->parseBuffs($name), $damage, $health);

                if($boss instanceof BossEntity){
                        if(isset($this->bosses[$name]) && $this->bosses[$name]->isAlive()){
                                $this->bosses[$name]->close();
                        }

                        if(isset($this->timers[$name])){
                                $this->timers[$name]->cancel();
                        }

                        $boss->setName($name);
                        $boss->setSkin($this->parseSkin($name));

                        $boss->setNameTagAlwaysVisible(true);
                        $boss->setHealth($health);
                        $boss->setScale(2);

                        $boss->spawnToAll();

                        $boss->respawns = RandomUtils::toBool($this->core->module_configurations->bosses[$name]["respawns"] ?? true);

                        $item = $this->parseHandItem($name);
                        $item->getNamedTag()->setTag("ench", new ListTag([]));

                        $boss->getInventory()->setHeldItemIndex(0);
                        $boss->getInventory()->setItem(0, $item);

                        $this->bosses[$name] = $boss;

                        return true;
                }

                return false;
        }


        /**
         * @param string $name
         *
         * @return Skin|null
         * @throws \JsonException
         */
        public function parseSkin(string $name): ?Skin{
                return new Skin("Standard_Custom", $this->parseSkinData($name), "", CustomGeo::GEO[$name]["name"] ?? "", CustomGeo::GEO[$name]["data"] ?? "");
        }

        /**
         * @param string $name
         *
         * @return string|null
         */
        public function parseSkinData(string $name): ?string{
                if(!isset($this->core->module_configurations->bosses[$name])) return null;

                $skin = $this->core->module_configurations->bosses[$name]["skin"];
                return SkinUtils::skinFromPNGFile($this->core->getDataFolder() . "boss_skins/" . $skin);
        }

        /**
         * @param string $name
         *
         * @return Item|null
         */
        public function parseHandItem(string $name): ?Item{
                if(!isset($this->core->module_configurations->bosses[$name])) return null;

                list($id, $meta) = explode(":", $this->core->module_configurations->bosses[$name]["hand_item"]);
                return ItemFactory::getInstance()->get((int) $id, (int) $meta);
        }

        /**
         * @param string $name
         *
         * @return array
         */
        public function parseBuffs(string $name): array{
                if(!isset($this->core->module_configurations->bosses[$name])) return [];

                $result = [];

                $buffs = $this->core->module_configurations->bosses[$name]["buffs"];
                if(count($buffs) <= 0) return [];

                $sounds = $buffs["sounds"];
                $effects = $buffs["effects"];

                foreach($sounds as $sound){
                        $result[0][] = explode(":", $sound);
                }

                foreach($effects as $effect){
                        list($percent, $effect) = explode("%", $effect);

                        $output = RandomUtils::parseEffects($effect);
                        $output = empty($output) ? (substr($effect, 0, 5) === "code:" ? substr($effect, 5) : explode(";", $effect)) : $output[0];

                        $result[1][] = [$percent, $output];
                }

                return $result;
        }
}