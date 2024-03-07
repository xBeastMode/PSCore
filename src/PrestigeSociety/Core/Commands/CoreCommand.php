<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\PrestigeSocietyCore;
abstract class CoreCommand extends Command implements PluginOwned{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * CoreCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         * @param string              $command
         * @param string              $description
         * @param string              $usage
         * @param array               $aliases
         */
        public function __construct(PrestigeSocietyCore $plugin, string $command, string $description, string $usage, array $aliases){
                parent::__construct($command, $description, $usage, $aliases);
                $this->core = $plugin;
        }

        /**
         * @param CommandSender $sender
         *
         * @return bool
         */
        public function testPlayer(CommandSender $sender): bool{
                if(!$sender instanceof Player){
                        $sender->sendMessage("Please run this command in-game.");
                        return false;
                }
                return true;
        }

        /**
         * @param CommandSender $sender
         *
         * @return bool
         */
        public function testPlayerSilent(CommandSender $sender): bool{
                return $sender instanceof Player;
        }

        /**
         * @param CommandSender $sender
         *
         * @return bool
         */
        public function testAll(CommandSender $sender): bool {
                if(!$this->testPlayer($sender)){
                        return false;
                }
                return $this->testPermission($sender);
        }

        /**
         * @param CommandSender $sender
         * @param string        $permission
         *
         * @return bool
         */
        public function testCustomPermission(CommandSender $sender, string $permission): bool{
                if(!$sender->hasPermission($permission)){
                        $sender->sendMessage($sender->getServer()->getLanguage()->translateString(TextFormat::RED . "%commands.generic.permission"));
                        return false;
                }
                return true;
        }

        /**
         * @param CommandSender $sender
         * @param string        $permission
         *
         * @return bool
         */
        public function testPermissionAndPlayer(CommandSender $sender, string $permission): bool{
                if(!$sender->hasPermission($permission)){
                        $sender->sendMessage($sender->getServer()->getLanguage()->translateString(TextFormat::RED . "%commands.generic.permission"));
                        return false;
                }
                return $this->testPlayer($sender);
        }

        /**
         * @return PrestigeSocietyCore
         */
        public function getOwningPlugin(): Plugin{
                return $this->core;
        }
}