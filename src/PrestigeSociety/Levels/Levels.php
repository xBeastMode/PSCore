<?php
namespace PrestigeSociety\Levels;
use JetBrains\PhpStorm\Pure;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\ConsoleUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\Utils\StringUtils;

class Levels{
        const CRATE_NORMAL = "normal";
        const CRATE_VANILLA_ENCHANTMENTS = "vanilla_ench";
        const CRATE_CUSTOM_ENCHANTMENTS = "custom_ench";

        /** @var String[] */
        public array $players = [];

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var int[] */
        protected array $block_data_sessions = [];
        /** @var int[] */
        protected array $play_time_sessions = [];

        /**
         * Levels constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                foreach(array_keys($core->module_configurations->levels) as $level){
                        PermissionManager::getInstance()->addPermission(new Permission("level.$level", "Player reached prestige $level", [DefaultPermissions::ROOT_OPERATOR]));
                }

                $core->getServer()->getPluginManager()->registerEvents(new LevelsListener($core), $core);
        }

        /**
         * @param $player
         *
         * @return bool
         */
        public function playerExists($player): bool{
                return StaticLevels::playerExists($player);
        }

        /**
         * @param $player
         */
        public function addNewPlayer($player): void{
                StaticLevels::addNewPlayer($player);
        }

        /**
         * @param     $player
         * @param int $level
         */
        public function setLevel($player, int $level): void{
                StaticLevels::setLevel($player, $level);
        }

        /**
         * @param     $player
         * @param int $deaths
         */
        public function setDeaths($player, int $deaths): void{
                StaticLevels::setDeaths($player, $deaths);
        }

        /**
         * @param     $player
         * @param int $kills
         */
        public function setKills($player, int $kills): void{
                StaticLevels::setKills($player, $kills);
        }

        /**
         * @param     $player
         * @param int $value
         */
        public function setBlocksPlaced($player, int $value): void{
                StaticLevels::setBlocksPlaced($player, $value);
        }

        /**
         * @param     $player
         * @param int $value
         */
        public function setBlocksBroken($player, int $value): void{
                StaticLevels::setBlocksBroken($player, $value);
        }

        /**
         * @param     $player
         * @param int $value
         */
        public function setPlayTime($player, int $value): void{
                StaticLevels::setPlayTime($player, $value);
        }

        /**
         * @param     $player
         * @param int $value
         */
        public function setBossesKilled($player, int $value): void{
                StaticLevels::setBossesKilled($player, $value);
        }

        /**
         * @param $player
         *
         * @return int
         */
        public function getLevel($player): int{
                return StaticLevels::getLevel($player);
        }

        /**
         * @param $player
         *
         * @return int
         */
        public function getDeaths($player): int{
                return StaticLevels::getDeaths($player);
        }

        /**
         * @param $player
         *
         * @return int
         */
        public function getKills($player): int{
                return StaticLevels::getKills($player);
        }

        /**
         * @param $player
         *
         * @return int
         */
        public function getBlocksPlaced($player): int{
                return StaticLevels::getBlocksPlaced($player);
        }

        /**
         * @param $player
         *
         * @return int
         */
        public function getBlocksBroken($player): int{
                return StaticLevels::getBlocksBroken($player);
        }

        /**
         * @param $player
         *
         * @return int
         */
        public function getPlayTime($player): int{
                return StaticLevels::getPlayTime($player);
        }

        /**
         * @param $player
         *
         * @return int
         */
        public function getBossesKilled($player): int{
                return StaticLevels::getBossesKilled($player);
        }

        /**
         * @param int $amount
         *
         * @return int[]
         */
        public function getTopKills(int $amount): array{
                return StaticLevels::getTopKills($amount);
        }

        /**
         * @param int $amount
         *
         * @return int[][]
         */
        public function getTopDeaths(int $amount): array{
                return StaticLevels::getTopDeaths($amount);
        }

        /**
         * @param int $amount
         *
         * @return int[][]
         */
        public function getTopLevels(int $amount): array{
                return StaticLevels::getTopLevels($amount);
        }

        /**
         * @param int $amount
         *
         * @return int[][]
         */
        public function getTopPlayTime(int $amount): array{
                return StaticLevels::getTopPlayTime($amount);
        }

        /**
         * @param int $amount
         *
         * @return int[][]
         */
        public function getTopBossesKilled(int $amount): array{
                return StaticLevels::getTopBossesKilled($amount);
        }


        /**
         * @param $player
         */
        public function addTempBlockPlace($player){
                $player = RandomUtils::getName($player);
                if(!isset($this->block_data_sessions[$player])){
                        $this->block_data_sessions[$player] = [0, 0];
                }
                $this->block_data_sessions[$player][0] += 1;
        }

        /**
         * @param $player
         *
         * @return int
         */
        #[Pure] public function getTempBlockPlace($player): int{
                $player = RandomUtils::getName($player);
                return $this->block_data_sessions[$player][0] ?? 0;
        }

        /**
         * @param $player
         */
        public function addTempBlockBreak($player){
                $player = RandomUtils::getName($player);
                if(!isset($this->block_data_sessions[$player])){
                        $this->block_data_sessions[$player] = [0, 0];
                }
                $this->block_data_sessions[$player][1] += 1;
        }

        /**
         * @param $player
         *
         * @return int
         */
        #[Pure] public function getTempBlockBreak($player): int{
                $player = RandomUtils::getName($player);
                return $this->block_data_sessions[$player][1] ?? 0;
        }

        /**
         * @param $player
         */
        public function startPlayTime($player){
                $this->play_time_sessions[RandomUtils::getName($player)] = time();
        }

        /**
         * @param $player
         */
        public function endPlayTime($player){
                $player = RandomUtils::getName($player);

                $play_time = $this->play_time_sessions[$player] ?? 0;
                $play_time = ($play_time !== 0 ? time() - $play_time : 0);

                $this->setPlayTime($player, $this->getPlayTime($player) + $play_time);
        }

        /**
         * @param $player
         *
         * @return int
         */
        public function getTotalPlayTime($player): int{
                $player = RandomUtils::getName($player);

                $play_time = $this->play_time_sessions[$player] ?? 0;
                $play_time = ($play_time !== 0 ? time() - $play_time : 0);

                return $this->getPlayTime($player) + $play_time;
        }

        /**
         * @param $player
         *
         * @return string
         */
        public function getTotalPlayTimeToDHMS($player): string{
                $player = RandomUtils::getName($player);

                $play_time = $this->play_time_sessions[$player] ?? 0;
                $play_time = ($play_time !== 0 ? time() - $play_time : 0);

                $play_time = $this->getPlayTime($player) + $play_time;

                [$d, $h, $m, $s] = StringUtils::secondsToDHMS($play_time);
                return "{$d}d{$h}h{$m}m{$s}s";
        }

        public function saveTempBlockSessionsData(){
                foreach($this->block_data_sessions as $player => $data){
                        $this->setBlocksPlaced($player, $this->getBlocksPlaced($player) + $data[0]);
                        $this->setBlocksBroken($player, $this->getBlocksBroken($player) + $data[1]);
                }
        }

        /**
         * @param $player
         *
         * @return bool
         */
        public function levelUp($player): bool{
                $levels = $this->core->module_configurations->levels;
                $level = $this->getLevel($player) + 1;

                if(isset($levels[$level])){
                        $level_config = $levels[$level];
                        $rank = $this->core->module_loader->ranks->getRank($player);
                        if($rank === $level_config["required_rank"]){
                                $this->setLevel($player, $level);
                                foreach(array_merge($levels["global_commands"], $level_config["commands"]) as $command){
                                        ConsoleUtils::dispatchCommandAsConsole(str_replace(["@player", "@level"], [RandomUtils::getName($player), $level], $command));
                                }
                                return true;
                        }
                }
                return false;
        }
}