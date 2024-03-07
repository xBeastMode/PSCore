<?php

namespace PrestigeSociety\Core;
use _64FF00\PurePerms\PurePerms;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\Utils\ScoreFactory;
use PrestigeSociety\Economy\StaticEconomy;
use PrestigeSociety\Levels\StaticLevels;
use PrestigeSociety\Nicknames\StaticNicknames;
use PrestigeSociety\Ranks\StaticRanks;
class HUD{
        /** @var Player[] */
        public array $players = [];

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var string[] */
        public array $messages = [];
        /** @var string[][] */
        public array $display_name = [];
        /** @var int[] */
        public array $display_index = [];

        /** @var int */
        protected int $current_tick = 0;

        /**
         * HUD constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                foreach($core->module_configurations->hud as $key => $value){
                        if($key === "enable") continue;

                        $this->display_name[$key] = $value["title"];
                        $this->messages[$key] = implode("\n", $value["message"]);
                }
        }

        /**
         * @param Player $player
         */
        public function addPlayer(Player $player){
                $this->players[spl_object_hash($player)] = $player;
        }

        /**
         * @param Player $player
         *
         * @return bool
         */
        public function inPlayers(Player $player): bool{
                return isset($this->players[spl_object_hash($player)]);
        }

        /**
         * @param Player $player
         */
        public function removePlayer(Player $player){
                if($this->inPlayers($player)){
                        unset($this->players[spl_object_hash($player)]);
                }
        }

        /**
         * @param string $player
         * @param string $message
         *
         * @return string|array
         */
        public function formatStats(string $player, string $message): string|array{
                $money = StaticEconomy::getMoney($player);
                $kills = StaticLevels::getKills($player);
                $deaths = StaticLevels::getDeaths($player);
                $level = StaticLevels::getLevel($player);
                $nick = StaticNicknames::getNick($player);
                $rank = StaticRanks::getRank($player);

                $display = ($nick === null) ? "N/A" : "~" . $player;

                $search = [
                    "@money", "@kills", "@deaths", "@level", "@display_name", "@rank"
                ];

                $replace = [
                    $money, $kills, $deaths, $level, $display, $rank
                ];

                $message = str_replace($search, $replace, $message);
                return RandomUtils::colorMessage($message);
        }

        public function broadcastHUD(){
                foreach($this->players as $player){
                        $rounded = $player->getPosition()->round();
                        $world = (string) $player->getWorld()->getDisplayName();
                        $group = "unknown";
                        $nextRankCost = PrestigeSocietyCore::getInstance()->module_loader->ranks->getNextRankPrice($player);
                        $nextRankCost *= PrestigeSocietyCore::getInstance()->module_loader->levels->getLevel($player);
                        $nextRank = PrestigeSocietyCore::getInstance()->module_loader->ranks->getNextRank($player);

                        $pure_perms = $player->getServer()->getPluginManager()->getPlugin("PurePerms");

                        if($pure_perms instanceof PurePerms){
                                $group = $pure_perms->getUserDataMgr()->getGroup($player);
                                if($group !== null){
                                        $group = $group->getName();
                                }
                        }

                        $combatTime = $this->core->module_loader->combat_logger->getTime($player);
                        if($combatTime === 0){
                                $combatTime = TextFormat::GREEN . "out of combat";
                        }

                        $ping = $player->getNetworkSession()->getPing();
                        $coloredPing = "";

                        if($ping < 50){
                                $coloredPing = TextFormat::GREEN . $ping . "ms [VERY GOOD]";
                        }else if($ping >= 50 && $ping < 100){
                                $coloredPing = TextFormat::DARK_GREEN . $ping . "ms [GOOD]";
                        }elseif($ping > 100 && $ping < 200){
                                $coloredPing = TextFormat::GOLD . $ping . "ms [OKAY]";
                        }elseif($ping >= 200 && $ping < 250){
                                $coloredPing = TextFormat::RED . $ping . "ms [BAD]";
                        }elseif($ping >= 250){
                                $coloredPing = TextFormat::DARK_RED . $ping . "ms [VERY BAD]";
                        }

                        $stats = $this->formatStats($player->getName(), $this->messages[$world] ?? $this->messages["default"]);
                        $stats = str_replace([
                            "@x", "@y", "@z", "@world", "@ping", "@colored_ping", "@group", "@name", "@next_rank_cost", "@next_rank", "@combat_time"
                        ], [
                            $rounded->x, $rounded->y, $rounded->z, $world, $ping, $coloredPing, $group, $player->getName(), $nextRankCost, $nextRank, $combatTime
                        ], $stats);
                        $stats = explode("\n", $stats);

                        if(!isset($this->display_index[$world])){
                                $this->display_index[$world] = 0;
                        }
                        
                        $display_name = $this->display_name[$world] ?? $this->display_name["default"];

                        if(++$this->display_index[$world] > (count($display_name) - 1)){
                                $this->display_index[$world] = 0;
                        }

                        ScoreFactory::setScore($player, RandomUtils::colorMessage($display_name[$this->display_index[$world]]));

                        foreach($stats as $index => $stat){
                                if(($index + 1) <= 15){
                                        ScoreFactory::setScoreLine($player, ($index + 1), $stat);
                                }
                        }
                }
        }

        /**
         * @param Player $player
         *
         * @return bool
         */
        public function toggleHUD(Player $player): bool{
                $event = $this->core->module_loader->events->onToggleHUD($player, !$this->inPlayers($player));

                if(!$event->isCancelled()){
                        if(!$event->isEnabled()){
                                ScoreFactory::removeScore($player);
                                $this->removePlayer($player);
                                $player->sendPopup(RandomUtils::colorMessage("&l&8» &6HUD disabled"));
                        }else{
                                $this->addPlayer($player);
                                $player->sendPopup(RandomUtils::colorMessage("&l&8» &6HUD enabled"));
                        }
                        return true;
                }
                return false;
        }
}