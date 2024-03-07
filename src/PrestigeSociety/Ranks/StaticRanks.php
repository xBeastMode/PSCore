<?php
namespace PrestigeSociety\Ranks;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\DataModels\RanksModel;
class StaticRanks{
        /** @var array */
        public static array $rank_names = [];
        /** @var array */
        public static array $rank_indexes = [];

        /**
         * @param $player
         *
         * @return bool
         */
        public static function isPlayerRegistered($player): bool{
                return RanksModel::query()->where('name', '=', RandomUtils::getName($player))->exists();
        }

        /**
         * @param        $player
         * @param string $rank
         *
         * @return bool
         */
        public static function playerHasRank($player, string $rank): bool{
                $record = RanksModel::query()->where('name', '=', RandomUtils::getName($player));

                if($record->exists()){
                        return $record->value('rank') === $rank;
                }

                return false;
        }

        /**
         * @param             $player
         * @param string|null $defaultRank
         */
        public static function registerPlayer($player, ?string $defaultRank = null){
                RanksModel::query()->create(['name' => RandomUtils::getName($player), 'rank' => $defaultRank ?? self::$rank_names[0] ?? 'A']);
        }

        /**
         * @param        $player
         * @param string $rank
         *
         * @return bool
         */
        public static function setRank($player, string $rank): bool{
                if(!isset(self::$rank_indexes[$rank])){
                        return false;
                }

                $record = RanksModel::query()->where('name', '=', RandomUtils::getName($player));
                if($record->exists()){
                        $record->update(['rank' => $rank]);
                        return true;
                }

                self::registerPlayer($player, $rank);

                return true;
        }

        /**
         * @param $player
         *
         * @return mixed
         */
        public static function getRank($player): mixed{
                $record = RanksModel::query()->where('name', '=', RandomUtils::getName($player));
                if($record->exists()){
                        return $record->value('rank');
                }

                return null;
        }
}