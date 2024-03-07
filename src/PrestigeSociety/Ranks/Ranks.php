<?php
namespace PrestigeSociety\Ranks;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\ConsoleUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\DataModels\RanksModel;
class Ranks{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var array */
        protected mixed $ranks = [];
        /** @var array */
        protected array $rank_names = [];
        /** @var array */
        protected array $rank_indexes = [];
        /** @var array */
        protected array $rank_prices = [];
        /** @var array */
        protected array $rank_commands = [];

        /**
         * Ranks constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
                $this->ranks = yaml_parse_file($this->core->getDataFolder() . "ranks.yml");

                $i = 0;
                foreach($this->ranks as $rank => $price){
                        $this->rank_names[$i] = $rank;
                        $this->rank_indexes[$rank] = $i;
                        $this->rank_prices[$i] = $price["price"];
                        $this->rank_commands[$i] = $price["commands"];
                        ++$i;
                }

                StaticRanks::$rank_names = $this->rank_names;
                StaticRanks::$rank_indexes = $this->rank_indexes;
        }

        /**
         * @return string[]
         */
        public function getAllRanks(): array{
                return $this->rank_names;
        }

        /**
         * @return string
         */
        public function getFirstRank(): string{
                return $this->rank_names[0];
        }

        /**
         * @param $player
         *
         * @return mixed
         */
        public function getNextRank($player): mixed{
                $rank = $this->getRank($player);
                $rankIndex = $this->rank_indexes[$rank];
                return $this->rank_names[$rankIndex + 1] ?? $rank;
        }

        /**
         * @param $player
         *
         * @return mixed
         */
        public function getNextRankPrice($player): mixed{
                $rank = $this->getRank($player);
                $rankIndex = $this->rank_indexes[$rank];
                return $this->rank_prices[$rankIndex + 1] ?? 0;
        }

        /**
         * @return mixed
         */
        public function getLastRank(): string{
                return end($this->rank_names);
        }

        /**
         * @param $player
         *
         * @return bool
         */
        public function isPlayerRegistered($player): bool{
                return RanksModel::query()->where("name", "=", RandomUtils::getName($player))->exists();
        }

        /**
         * @param        $player
         * @param string $rank
         *
         * @return bool
         */
        public function playerHasRank($player, string $rank): bool{
                $record = RanksModel::query()->where("name", "=", RandomUtils::getName($player));

                if($record->exists()){
                        return $record->value("rank") === $rank;
                }

                return false;
        }

        /**
         * @param             $player
         * @param string|null $defaultRank
         */
        public function registerPlayer($player, ?string $defaultRank = null){
                RanksModel::query()->create(["name" => RandomUtils::getName($player), "rank" => $defaultRank ?? $this->rank_names[0] ?? "A"]);
        }

        /**
         * @param        $player
         * @param string $rank
         *
         * @return bool
         */
        public function setRank($player, string $rank): bool{
                if(!isset($this->rank_indexes[$rank])){
                        return false;
                }

                $record = RanksModel::query()->where("name", "=", RandomUtils::getName($player));
                if($record->exists()){
                        $record->update(["rank" => $rank]);
                        return true;
                }

                $this->registerPlayer($player, $rank);

                return true;
        }

        /**
         * @param $player
         * 
         * @return mixed|null
         */
        public function getRank($player): ?string{
                $record = RanksModel::query()->where("name", "=", RandomUtils::getName($player));
                if($record->exists()){
                        return $record->value("rank");
                }

                return null;
        }

        /**
         * @param $player
         * 
         * @return int
         */
        public function rankUp($player): int{
                $rank = $this->getRank($player);
                if($rank === null){
                        $this->registerPlayer($player);
                }

                $rank = $this->getRank($player);
                $money = $this->core->module_loader->economy->getMoney($player);
                $nextRankPrice = $this->getNextRankPrice($player) * $this->core->module_loader->levels->getLevel($player);

                if($money < $nextRankPrice){
                        return 2;
                }

                $last = $this->getLastRank();
                if($last === $rank){
                        return 1;
                }

                $nextRank = $this->getNextRank($player);
                $this->setRank($player, $nextRank);

                $index = $this->rank_indexes[$nextRank];
                $this->core->module_loader->economy->subtractMoney($player, $nextRankPrice);

                foreach($this->rank_commands[$index] as $command){
                        ConsoleUtils::dispatchCommandAsConsole(str_replace("@player", ($player instanceof Player) ? $player->getName() : $player, $command));
                }

                return 0;
        }
}