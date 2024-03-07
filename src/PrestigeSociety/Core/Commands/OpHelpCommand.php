<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\Utils\ServerUtils;
class OpHelpCommand extends CoreCommand{
        /**
         * OpHelpCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "ophelp", "Ask for op help!", RandomUtils::colorMessage("&eUsage: /ophelp <explain...>"), ["oph"]);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(count($args) <= 0){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }
                $reason = implode(" ", $args);

                $msg = str_replace(["@explanation", "@player"], [$reason, $sender->getName()], $this->core->getConfig()->get("op_help_format"));
                $msg = RandomUtils::colorMessage($msg);

                ServerUtils::broadcastToOps($msg);
                $sender->sendMessage($msg);

                return true;
        }
}