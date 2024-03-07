<?php
namespace PrestigeSociety\Levels;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\DataModels\LevelsModel;
class StaticLevels{
        /**
         * @param $player
         *
         * @return bool
         */
        public static function playerExists($player): bool{
                return LevelsModel::query()->where("name", "=", RandomUtils::getName($player))->exists();
        }

        /**
         * @param $player
         */
        public static function addNewPlayer($player): void{
                LevelsModel::query()->create([
                    "name" => RandomUtils::getName($player),
                    "level" => 1,
                    "blocks_broken" => 0,
                    "blocks_placed" => 0,
                    "kills" => 0,
                    "deaths" => 0,
                    "play_time" => 0,
                    "bosses_killed" => 0]);
        }

        /**
         * @param     $player
         * @param int $level
         */
        public static function setLevel($player, int $level): void{
                LevelsModel::query()->where("name", "=", RandomUtils::getName($player))->update(["level" => $level]);
        }

        /**
         * @param     $player
         * @param int $deaths
         */
        public static function setDeaths($player, int $deaths): void{
                LevelsModel::query()->where("name", "=", RandomUtils::getName($player))->update(["deaths" => $deaths]);
        }

        /**
         * @param     $player
         * @param int $kills
         */
        public static function setKills($player, int $kills): void{
                LevelsModel::query()->where("name", "=", RandomUtils::getName($player))->update(["kills" => $kills]);
        }

        /**
         * @param     $player
         * @param int $value
         */
        public static function setBlocksPlaced($player, int $value): void{
                LevelsModel::query()->where("name", "=", RandomUtils::getName($player))->update(["blocks_placed" => $value]);
        }

        /**
         * @param     $player
         * @param int $value
         */
        public static function setBlocksBroken($player, int $value): void{
                LevelsModel::query()->where("name", "=", RandomUtils::getName($player))->update(["blocks_broken" => $value]);
        }

        /**
         * @param     $player
         * @param int $value
         */
        public static function setPlayTime($player, int $value): void{
                LevelsModel::query()->where("name", "=", RandomUtils::getName($player))->update(["play_time" => $value]);
        }

        /**
         * @param     $player
         * @param int $value
         */
        public static function setBossesKilled($player, int $value): void{
                LevelsModel::query()->where("name", "=", RandomUtils::getName($player))->update(["bosses_killed" => $value]);
        }

        /**
         * @param $player
         *
         * @return int
         */
        public static function getLevel($player): int{
                $record = LevelsModel::query()->where("name", "=", RandomUtils::getName($player));
                if(!$record->exists()){
                        self::addNewPlayer($player);
                        return 0;
                }
                return $record->value("level");
        }

        /**
         * @param $player
         *
         * @return int
         */
        public static function getDeaths($player): int{
                $record = LevelsModel::query()->where("name", "=", RandomUtils::getName($player));
                if(!$record->exists()){
                        self::addNewPlayer($player);
                        return 0;
                }
                return $record->value("deaths");
        }

        /**
         * @param $player
         *
         * @return int
         */
        public static function getKills($player): int{
                $record = LevelsModel::query()->where("name", "=", RandomUtils::getName($player));
                if(!$record->exists()){
                        self::addNewPlayer($player);
                        return 0;
                }
                return $record->value("kills");
        }

        /**
         * @param int $amount
         *
         * @return int[]
         */
        public static function getTopKills(int $amount): array{
                return LevelsModel::query()->orderBy("kills", "desc")->take($amount)->get()->toArray();
        }

        /**
         * @param int $amount
         *
         * @return int[][]
         */
        public static function getTopDeaths(int $amount): array{
                return LevelsModel::query()->orderBy("deaths", "desc")->take($amount)->get()->toArray();
        }

        /**
         * @param int $amount
         *
         * @return int[]
         */
        public static function getTopPlayTime(int $amount): array{
                return LevelsModel::query()->orderBy("play_time", "desc")->take($amount)->get()->toArray();
        }

        /**
         * @param int $amount
         *
         * @return int[][]
         */
        public static function getTopBossesKilled(int $amount): array{
                return LevelsModel::query()->orderBy("bosses_killed", "desc")->take($amount)->get()->toArray();
        }

        /**
         * @param $player
         *
         * @return int
         */
        public static function getBlocksPlaced($player): int{
                $record = LevelsModel::query()->where("name", "=", RandomUtils::getName($player));
                if(!$record->exists()){
                        self::addNewPlayer($player);
                        return 0;
                }
                return $record->value("blocks_placed");
        }

        /**
         * @param $player
         *
         * @return int
         */
        public static function getBlocksBroken($player): int{
                $record = LevelsModel::query()->where("name", "=", RandomUtils::getName($player));
                if(!$record->exists()){
                        self::addNewPlayer($player);
                        return 0;
                }
                return $record->value("blocks_broken");
        }

        /**
         * @param $player
         *
         * @return int
         */
        public static function getPlayTime($player): int{
                $record = LevelsModel::query()->where("name", "=", RandomUtils::getName($player));
                if(!$record->exists()){
                        self::addNewPlayer($player);
                        return 0;
                }
                return $record->value("play_time");
        }

        /**
         * @param $player
         *
         * @return int
         */
        public static function getBossesKilled($player): int{
                $record = LevelsModel::query()->where("name", "=", RandomUtils::getName($player));
                if(!$record->exists()){
                        self::addNewPlayer($player);
                        return 0;
                }
                return $record->value("bosses_killed");
        }

        /**
         * @param int $amount
         *
         * @return int[][]
         */
        public static function getTopLevels(int $amount): array{
                return LevelsModel::query()->orderBy("level", "desc")->take($amount)->get()->toArray();
        }
}