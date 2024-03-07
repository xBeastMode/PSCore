<?php
namespace PrestigeSociety\Bosses\Commands;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class RemoveBossCommand extends CoreCommand{
        /**
         * RemoveBossCommand constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                parent::__construct($core,"removeboss", "Remove a boss spawn", RandomUtils::colorMessage("&eUsage: /removeboss <name: string>"), []);
                $this->setPermission("command.removeboss");
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         *
         * @throws CommandException
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPermission($sender)){
                        return false;
                }

                if(count($args) < 1){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                $name = $args[0];
                if($this->core->module_loader->bosses->removeBoss($name)){
                        $msg = $this->core->getMessage("bosses", "boss_removed");
                }else{
                        $msg = $this->core->getMessage("bosses", "unknown_boss");
                }

                $msg = str_replace("@name", $name, $msg);
                $sender->sendMessage(RandomUtils::colorMessage($msg));

                return true;
        }
}