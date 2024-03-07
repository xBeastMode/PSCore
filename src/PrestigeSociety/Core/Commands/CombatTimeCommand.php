<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class CombatTimeCommand extends CoreCommand{
        /**
         * CombatTimeCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "combattime", "Check how much combat time you have left", RandomUtils::colorMessage("&e/ct"), ["ct"]);
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

                $time = $this->core->module_loader->combat_logger->getTime($sender);
                $msg = RandomUtils::colorMessage($this->core->getMessage("combat_logger", "combat_time"));
                $msg = str_replace(["@time", "@literal"], [$time === 0 ? "out of combat" : $time . " seconds", $time], $msg);
                $sender->sendMessage($msg);

                return true;
        }

}