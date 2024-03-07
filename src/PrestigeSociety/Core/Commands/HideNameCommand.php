<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class HideNameCommand extends CoreCommand{
        /**
         * HideNameCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.hidename");
                parent::__construct($plugin, "hidename", "Hide another player's name", RandomUtils::colorMessage("&eUsage: /hidename [player]"), []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return false
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPermission($sender)){
                        return false;
                }

                if(count($args) === 1){
                        $player = $sender->getServer()->getPlayerByPrefix($args[0]);
                        if($player === null){
                                $sender->sendMessage(TextFormat::RED . "$args[0] is offline.");
                                return false;
                        }

                        $this->core->module_loader->fun_box->toggleHideName($sender, $player);
                }else{
                        if(!$this->testPlayer($sender)){
                                return false;
                        }

                        /** @var Player|CommandSender $sender */
                        $this->core->module_loader->fun_box->toggleHideName($sender, $sender);
                }

                return false;
        }
}