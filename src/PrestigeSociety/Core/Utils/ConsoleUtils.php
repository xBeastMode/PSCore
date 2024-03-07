<?php
namespace PrestigeSociety\Core\Utils;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\Language;
use PrestigeSociety\Core\PrestigeSocietyCore;
class ConsoleUtils{
        /**
         * @param array  $lines
         * @param string $breakLine
         */
        public static function logArray(array $lines, string $breakLine = "\n"){
                PrestigeSocietyCore::getInstance()->getServer()->getLogger()->info(implode($breakLine, $lines));
        }

        /**
         * @param array $string
         */
        public static function log(...$string){
                self::logArray($string);
        }

        /**
         * @return ConsoleCommandSender
         */
        public static function newConsoleCommandSender(): ConsoleCommandSender{
                return new ConsoleCommandSender(PrestigeSocietyCore::getInstance()->getServer(), new Language(Language::FALLBACK_LANGUAGE));
        }

        /**
         * @param string $command
         */
        public static function dispatchCommandAsConsole(string $command){
                PrestigeSocietyCore::getInstance()->getServer()->dispatchCommand(self::newConsoleCommandSender(), $command);
        }
}