<?php
namespace PrestigeSociety\Crates;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\DataModels\CratesModel;
class StaticCrates{
        const TYPE_BASIC_CRATE = "basic_crate";
        const TYPE_OP_CRATE = "op_crate";
        const TYPE_EXCLUSIVE_CRATE = "exclusive_crate";
        const TYPE_VOTE_CRATE = "vote_crate";
        const TYPE_WEAPON_CRATE = "weapon_crate";

        /**
         * @param string $type
         *
         * @return bool
         */
        public static function validateCrateType(string $type): bool{
                return in_array($type, [
                    self::TYPE_BASIC_CRATE,
                    self::TYPE_OP_CRATE,
                    self::TYPE_EXCLUSIVE_CRATE,
                    self::TYPE_VOTE_CRATE,
                    self::TYPE_WEAPON_CRATE
                ]);
        }

        /**
         * @param $player
         *
         * @return bool
         */
        public static function playerExists($player): bool{
                return CratesModel::query()->where("name", "=", RandomUtils::getName($player))->exists();
        }

        /**
         * @param $player
         */
        public static function addNewPlayer($player): void{
                CratesModel::query()->create([
                    "name" => RandomUtils::getName($player),
                    "basic_crate" => 0,
                    "op_crate" => 0,
                    "exclusive_crate" => 0,
                    "vote_crate" => 0,
                    "weapon_crate" => 0,
                ]);
        }

        /**
         * @param        $player
         * @param string $type
         *
         * @return int|mixed
         */
        public static function getCrateCount($player, string $type){
                if(!static::validateCrateType($type)) return 0;

                $record = CratesModel::query()->where("name", "=", RandomUtils::getName($player));
                if(!$record->exists()){
                        static::addNewPlayer($player);
                        return 0;
                }

                return $record->value($type);
        }

        /**
         * @param        $player
         * @param string $type
         * @param int    $count
         *
         * @return bool
         */
        public static function setCrateCount($player, string $type, int $count): bool{
                if(!static::validateCrateType($type)) return false;

                CratesModel::query()->where("name", "=", RandomUtils::getName($player))->update([$type => $count]);
                return true;
        }

        /**
         * @param        $player
         * @param string $type
         * @param int    $count
         *
         * @return bool
         */
        public static function addCrateCount($player, string $type, int $count = 1): bool{
                return static::setCrateCount($player, $type, static::getCrateCount($player, $type) + $count);
        }

        /**
         * @param        $player
         * @param string $type
         * @param int    $count
         *
         * @return bool
         */
        public static function subtractCrateCount($player, string $type, int $count = 1): bool{
                return static::setCrateCount($player, $type, static::getCrateCount($player, $type) - $count);
        }
}