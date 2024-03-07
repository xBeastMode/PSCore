<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\player\Player;
use pocketmine\world\World;
use PrestigeSociety\Core\PrestigeSocietyCore;
use pocketmine\command\CommandSender;
class WorldCommand extends CoreCommand{
        /**
         * WorldCommand constructor.
         * 
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.world");
                parent::__construct($plugin, "world", "teleport to another world", "/world <world>", ["cw"]);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testAll($sender)){
                        return false;
                }

                if(!isset($args[0])){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                if(strtolower($args[0]) === "list"){
                        $sender->sendMessage("Worlds: " . implode(", ", array_map(function (World $level){ return $level->getDisplayName(); }, $sender->getServer()->getWorldManager()->getWorlds())));
                        return false;
                }

                /** @var Player $sender */

                if(!$this->core->getServer()->getWorldManager()->isWorldLoaded($args[0])){
                        $this->core->getServer()->getWorldManager()->loadWorld($args[0], true);

                }
                $level = $this->core->getServer()->getWorldManager()->getWorldByName($args[0]);
                if($level !== null){
                        $sender->sendMessage("Teleporting...");
                        $sender->teleport($level->getSpawnLocation());
                }else{
                        $sender->sendMessage("Level does not exist.");
                }

                return true;
        }
}