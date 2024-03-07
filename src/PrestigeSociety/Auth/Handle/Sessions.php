<?php
namespace PrestigeSociety\Auth\Handle;
use pocketmine\Player;
class Sessions{
        /** @var Player[] */
        private static $waiting = [];

        /**
         * @param Player $player
         */
        public static function addUnAuthed(Player $player){
                self::$waiting[$player->getName()] = $player;
        }

        /**
         * @param Player $player
         */
        public static function removeUnAuthed(Player $player){
                if(self::isUnAuthed($player))
                        unset(self::$waiting[$player->getName()]);
        }

        /**
         * @param Player $player
         * 
         * @return bool
         */
        public static function isUnAuthed(Player $player): bool{
                return isset(self::$waiting[$player->getName()]);
        }

        /**
         * @return Player[]
         */
        public static function getUnAuthed(): array{
                return self::$waiting;
        }
}