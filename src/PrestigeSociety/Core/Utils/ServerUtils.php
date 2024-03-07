<?php
namespace PrestigeSociety\Core\Utils;
use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;
use pocketmine\world\World;
use PrestigeSociety\Core\PrestigeSocietyCore;
class ServerUtils{
        /** @var string */
        protected const SERVER_ADDRESS = "play.euphmc.net";

        /**
         * @param string $reason
         */
        public static function killAllPlayers(string $reason = ""){
                foreach(PrestigeSocietyCore::getInstance()->getServer()->getOnlinePlayers() as $player){
                        $player->kick($reason);
                }
        }

        public static function transferAllPlayers(){
                $core = PrestigeSocietyCore::getInstance();
                foreach($core->getServer()->getOnlinePlayers() as $player){
                        $player->transfer(self::SERVER_ADDRESS, $core->getServer()->getPort());
                }
        }

        /**
         * @param string $reason
         */
        public static function ballAllPlayers(string $reason = ""){
                foreach(PrestigeSocietyCore::getInstance()->getServer()->getOnlinePlayers() as $player){
                        PrestigeSocietyCore::getInstance()->getServer()->getNameBans()->addBan($player->getName(), $reason);
                }
        }

        /**
         * @param string $reason
         */
        public static function kickAndShutDown(string $reason = ""){
                self::killAllPlayers($reason);
                PrestigeSocietyCore::getInstance()->getServer()->shutdown();
        }

        public static function transferAndShutDown(){
                self::transferAllPlayers();
                PrestigeSocietyCore::getInstance()->getServer()->shutdown();
        }

        /**
         * @param string $message
         */
        public static function broadcastMessage(string $message){
                PrestigeSocietyCore::getInstance()->getServer()->broadcastMessage($message);
        }

        /**
         * @return World[]
         */
        #[Pure] public static function getLevels(): array{
                return PrestigeSocietyCore::getInstance()->getServer()->getWorldManager()->getWorlds();
        }

        /**
         * @param string $message
         */
        public static function broadcastToOps(string $message){
                foreach(self::getOnlineOps() as $player){
                        $player->sendMessage($message);
                }
        }

        /**
         * @return Player[]
         */
        public static function getOnlineOps(): array{
                return array_filter(self::getOnlinePlayers(), function (Player $player){ return PrestigeSocietyCore::getInstance()->getServer()->isOp($player->getName()); });
        }

        /**
         * @param string $title
         * @param string $subtitle
         * @param int    $in
         * @param int    $stay
         * @param int    $out
         */
        public static function broadcastTitle(string $title, string $subtitle, int $in = 20, int $stay = 20, int $out = 20){
                PrestigeSocietyCore::getInstance()->getServer()->broadcastTitle($title, $subtitle, $in, $stay, $out);
        }

        /**
         * @return Player[]
         */
        #[Pure] public static function getOnlinePlayers(): array{
                return PrestigeSocietyCore::getInstance()->getServer()->getOnlinePlayers();
        }
}