<?php
namespace PrestigeSociety\Teleport\Handle;
use pocketmine\player\Player;
class TeleportQueue{
        /** @var Player[]|int[] */
        private static array $teleport_queue = [];

        /**
         * @param Player $player
         */
        public static function addToQueue(Player $player){
                self::$teleport_queue[spl_object_hash($player)] = $player;
        }

        /**
         * @param Player $player
         */
        public static function removeFromQueue(Player $player){
                if(self::isInQueue($player))
                        unset(self::$teleport_queue[spl_object_hash($player)]);
        }

        /**
         * @param Player $player
         *
         * @return bool
         */
        public static function isInQueue(Player $player): bool{
                return isset(self::$teleport_queue[spl_object_hash($player)]);
        }

        /**
         * @param Player $player
         *
         * @return Player|null
         */
        public static function getFromQueue(Player $player): ?Player{
                if(self::isInQueue($player)){
                        return self::$teleport_queue[spl_object_hash($player)];
                }
                return null;
        }

        /**
         * @return Player[]
         */
        public static function getQueue(): ?array{
                return self::$teleport_queue;
        }
}