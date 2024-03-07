<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class FreezeCommand extends CoreCommand{
        /**
         * FreezeCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.freeze");
                parent::__construct($plugin, "freeze", "Freeze another player", RandomUtils::colorMessage("&eUsage: /freeze [player] [cancel-commands]"), []);
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

                /** @var Player $sender */

                if(count($args) > 0){
                        $player = $sender->getServer()->getPlayerByPrefix($args[0]);
                        if($player === null){
                                $sender->sendMessage(TextFormat::RED . "$args[0] is offline.");
                                return false;
                        }

                        $cancelCommands = $args[1] ?? false;
                        $cancelCommands = RandomUtils::toBool($cancelCommands);

                        $this->core->module_loader->fun_box->toggleFreeze($sender, $player, $cancelCommands);
                }else{
                        $this->core->module_loader->fun_box->toggleFreeze($sender, $sender, false);
                }
                return true;
        }
}