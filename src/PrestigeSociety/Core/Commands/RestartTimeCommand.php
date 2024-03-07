<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class RestartTimeCommand extends CoreCommand{
        /**
         * RestartTimeCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "restarttime", "Check when the server will restart", RandomUtils::colorMessage("&eUsage: /rt"), ["rt"]);
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
                $txt = $this->core->getMessage("restarter", "restart_time");
                $txt = RandomUtils::colorMessage($txt);
                $txt = RandomUtils::restarterTextReplacer($txt);
                $sender->sendMessage($txt);

                return true;
        }
}