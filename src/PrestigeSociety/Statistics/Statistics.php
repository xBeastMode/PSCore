<?php
namespace PrestigeSociety\Statistics;
use pocketmine\entity\EntityDataHelper as Helper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\world\World;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\Utils\SkinUtils;
use PrestigeSociety\Core\Utils\StringUtils;
use PrestigeSociety\Player\Data\Settings;
use PrestigeSociety\Statistics\Entity\StatHuman;
use PrestigeSociety\Statistics\Task\UpdateStatsTask;
class Statistics{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;
        /** @var int[] */
        protected array $skin_sessions = [];

        /**
         * Statistics constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                $this->core->getServer()->getPluginManager()->registerEvents(new StatisticsListener($core), $core);
                $this->core->getScheduler()->scheduleRepeatingTask(new UpdateStatsTask($core), 20 * 30);

                EntityFactory::getInstance()->register(StatHuman::class, function(World $world, CompoundTag $nbt) : StatHuman{
                        return new StatHuman(Helper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
                }, ["StatHuman"]);
        }

        public function updateStats(){
                foreach($this->core->getServer()->getWorldManager()->getWorlds() as $level){
                        foreach($level->getEntities() as $entity){
                                if($entity instanceof StatHuman){
                                        $this->load($entity);
                                }
                        }
                }
        }

        /**
         * @param Player $player
         * @param string $type
         * @param int    $place
         */
        public function addNew(Player $player, string $type, int $place){
                $nbt = RandomUtils::generateSkinCompoundTag($player->getSkin()->getSkinData());
                $human = new StatHuman($player->getLocation(), $player->getSkin(), $nbt);

                $human->type = $type;
                $human->place = $place;
                $human->statsProfile = $player->getName();

                $human->spawnToAll();
                $this->load($human);
        }

        /**
         * @param StatHuman $human
         */
        public function load(StatHuman $human){
                $type = $human->type;
                $place = $human->place;

                $categories = [
                    "kills" => ["kills", "levels", "getTopKills"],
                    "deaths" => ["deaths", "levels", "getTopDeaths"],
                    "money" => ["money", "economy", "getTopMoney"],
                    "levels" => ["level", "levels", "getTopLevels"],
                    "play_time" => ["play_time", "levels", "getTopPlayTime"],
                    "bosses_killed" => ["bosses_killed", "levels", "getTopBossesKilled"],
                ];

                $category = $categories[$type] ?? null;
                if($category !== null){
                        $type = $category[0];
                        $module = $category[1];
                        $function = $category[2];

                        $value = $this->core->module_loader->{$module}->{$function}($place)[$place - 1];
                        $player = $value["name"];

                        $statistic = $value[$type];
                        $statistic = $this->formatStat($player, $type, $statistic);

                        $settings = $this->core->module_loader->player_data->getPlayerSettings($player);

                        $human->setNameTag(RandomUtils::colorMessage(str_replace(
                            ["@player", "@value", "@place", "@status"],
                            [$player, $statistic, RandomUtils::ordinal($place), $settings->get(Settings::SETTING_PLAYER_STATUS)],
                            $this->core->getConfig()->get("statistics")[$type])));

                        $human->setNameTagVisible();
                        $human->setNameTagAlwaysVisible();

                        $this->setSkin($player, $human);
                        $human->statsProfile = $player;
                }
        }

        /**
         * @param string $player
         * @param Human  $human
         *
         * @return void
         */
        protected function setSkin(string $player, Human $human): void{
                $data = $this->core->getServer()->getOfflinePlayerData($player);
                $data = $data->getCompoundTag("Skin");

                $skinData = $data->getByteArray("Data");

                $capeData = $data->getByteArray("CapeData");
                $geometryName = $data->getString("GeometryName");
                $geometryData = $data->getByteArray("GeometryData");

                $id = $human->getId();
                $levelId = $human->getWorld()->getId();

                $this->core->module_loader->async_manager->submitAsyncQuery(function () use ($skinData, $capeData){
                        return [SkinUtils::skinToGreyscale($skinData), strlen($capeData) > 0 ? SkinUtils::capeToGreyscale($capeData) : ""];
                }, function ($data) use ($id, $levelId, $geometryName, $geometryData){
                        $skinData = $data[0];
                        $capeData = $data[1];

                        $human = PrestigeSocietyCore::getInstance()->getServer()->getWorldManager()->getWorld($levelId)->getEntity($id);

                        if($human instanceof Human){
                                $human->setSkin(new Skin($skinId = "Steve" . microtime(), $skinData, $capeData, $geometryName, $geometryData));
                                $human->sendSkin($human->getWorld()->getPlayers());
                                $human->sendData($human->getWorld()->getPlayers());
                        }
                });
        }

        /**
         * @param string $player
         * @param string $type
         * @param int    $statistic
         *
         * @return string
         */
        public function formatStat(string $player, string $type, int $statistic): string{
                if($type === "play_time"){
                        return $this->core->module_loader->levels->getTotalPlayTimeToDHMS($player);
                }
                return $statistic;
        }
}