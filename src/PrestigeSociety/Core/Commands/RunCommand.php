<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\PrestigeSocietyCore;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\Utils\ConsoleUtils;
class RunCommand extends CoreCommand{
        /**
         * RunCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.run");
                parent::__construct($plugin, "run", "run a command through multiple players", "Usage: /run <players> <command>", []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPermission($sender)){
                        return false;
                }

                if(count($args) < 2){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                $players = $args[0];
                array_shift($args);
                $command = implode(" ", $args);

                switch($players){
                        case "@all":
                                $players = $sender->getServer()->getOnlinePlayers();
                                foreach($players as $player){
                                        ConsoleUtils::dispatchCommandAsConsole(str_replace([
                                            "@player",
                                            "@rplayer",
                                            "@x",
                                            "@y",
                                            "@z",
                                            "@level",
                                            "@eid",
                                            "@xuid"
                                        ], [
                                            $player->getName(),
                                            $player->getName(),
                                            $player->getLocation()->x,
                                            $player->getLocation()->y,
                                            $player->getLocation()->z,
                                            $player->getWorld()->getDisplayName(),
                                            $player->getId(),
                                            $player->getXuid(),
                                        ], $command));
                                }
                                $this->core->sendMessage($sender, TextFormat::GREEN . "Ran command through " . count($players) . " player(s).");
                                break;
                        default:
                                $players = explode(",", $players);
                                foreach($players as $name){
                                        $player = $sender->getServer()->getPlayerByPrefix($name);
                                        if($player !== null){
                                                ConsoleUtils::dispatchCommandAsConsole(str_replace([
                                                    "@player",
                                                    "@rplayer",
                                                    "@x",
                                                    "@y",
                                                    "@z",
                                                    "@level",
                                                    "@eid",
                                                    "@xuid"
                                                ], [
                                                    $player->getName(),
                                                    $player->getName(),
                                                    $player->getLocation()->x,
                                                    $player->getLocation()->y,
                                                    $player->getLocation()->z,
                                                    $player->getWorld()->getDisplayName(),
                                                    $player->getId(),
                                                    $player->getXuid(),
                                                ], $command));
                                        }else{
                                                $this->core->sendMessage($sender, TextFormat::RED . "$name is offline.");
                                        }
                                        $this->core->sendMessage($sender, TextFormat::GREEN . "Ran command through " . count($players) . " player(s).");
                                }
                                break;
                }

                return true;
        }
}