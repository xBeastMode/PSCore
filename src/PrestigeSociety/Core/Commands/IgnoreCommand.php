<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class IgnoreCommand extends CoreCommand{
        /**
         * IgnoreCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "ignore", "ignore another player", RandomUtils::colorMessage("&eUsage: /ignore <player>"), ['silence']);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPlayer($sender)){
                        return false;
                }

                /** @var Player $sender */

                if(count($args) > 0){
                        $player = $sender->getServer()->getPlayerByPrefix($args[0]);
                        if($player === null){
                                $sender->sendMessage(TextFormat::RED . "$args[0] is offline.");
                                return false;
                        }
                        $this->core->module_loader->fun_box->toggleIgnore($sender, $player);
                }else{
                        $sender->sendMessage($this->getUsage());
                }

                return true;
        }
}