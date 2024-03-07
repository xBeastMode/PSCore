<?php
namespace PrestigeSociety\Credits;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\DataModels\CreditsModel;
class StaticCredits{
        /**
         * @param $player
         *
         * @return bool
         */
        public static function playerExists($player): bool{
                return CreditsModel::query()->where("name", "=", RandomUtils::getName($player))->exists();
        }

        /**
         * @param $player
         */
        public static function addNewPlayer($player): void{
                CreditsModel::query()->create(["name" => RandomUtils::getName($player), "credits" => 0]);
        }

        /**
         * @param $player
         * @param $money
         */
        public static function setCredits($player, $money): void{
                CreditsModel::query()->where("name", "=", RandomUtils::getName($player))->update(["credits" => $money]);
        }

        /**
         * @param $player
         * @param $money
         */
        public static function addCredits($player, $money): void{
                self::setCredits($player, self::getCredits($player) + $money);
        }

        /**
         * @param $player
         * @param $money
         *
         * @return bool
         */
        public static function subtractCredits($player, $money): bool{
                if(self::getCredits($player) - $money >= 0){
                        self::setCredits($player, self::getCredits($player) - $money);
                        return true;
                }
                return false;
        }

        /**
         * @param $from
         * @param $to
         * @param $money
         *
         * @return bool
         */
        public static function payCredits($from, $to, $money): bool{
                $fromMoney = self::getCredits($from);
                if(!($fromMoney <= $money)){
                        self::setCredits($to, $money);
                        self::subtractCredits($from, $money);
                        return true;
                }
                return false;
        }

        /**
         * @param $player
         *
         * @return int
         */
        public static function getCredits($player): int{
                $record = CreditsModel::query()->where("name", "=", RandomUtils::getName($player));
                if(!$record->exists()){
                        self::addNewPlayer($player);
                        return 0;
                }
                return $record->value("credits");
        }
}