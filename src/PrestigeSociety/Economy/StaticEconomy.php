<?php
namespace PrestigeSociety\Economy;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\DataModels\EconomyModel;
class StaticEconomy{
        /**
         * @param $player
         *
         * @return bool
         */
        public static function playerExists($player): bool{
                return EconomyModel::query()->where("name", "=", RandomUtils::getName($player))->exists();
        }

        /**
         * @param $player
         */
        public static function addNewPlayer($player): void{
                EconomyModel::query()->create(["name" => RandomUtils::getName($player), "money" => 0]);
        }

        /**
         * @param $player
         * @param $money
         */
        public static function setMoney($player, $money): void{
                EconomyModel::query()->where("name", "=", RandomUtils::getName($player))->update(["money" => $money]);
        }

        /**
         * @param $player
         * @param $money
         */
        public static function addMoney($player, $money): void{
                self::setMoney($player, self::getMoney($player) + $money);
        }

        /**
         * @param $player
         * @param $money
         *
         * @return bool
         */
        public static function subtractMoney($player, $money): bool{
                if(self::getMoney($player) - $money >= 0){
                        self::setMoney($player, self::getMoney($player) - $money);
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
        public static function payMoney($from, $to, $money): bool{
                $fromMoney = self::getMoney($from);
                if($fromMoney >= $money){
                        self::addMoney($to, $money);
                        self::subtractMoney($from, $money);
                        return true;
                }
                return false;
        }

        /**
         * @param $player
         *
         * @return int
         */
        public static function getMoney($player): int{
                $record = EconomyModel::query()->where("name", "=", RandomUtils::getName($player));
                if(!$record->exists()){
                        self::addNewPlayer($player);
                        return 0;
                }
                return $record->value("money");
        }

        /**
         * @param int $amount
         *
         * @return int[][]
         */
        public static function getTopMoney(int $amount): array{
                return EconomyModel::query()->orderBy("money", "desc")->take($amount)->get()->toArray();
        }
}