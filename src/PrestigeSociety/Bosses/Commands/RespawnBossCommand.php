<?php
namespace PrestigeSociety\Bosses\Commands;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class RespawnBossCommand extends CoreCommand{
        /**
         * RespawnBossCommand constructor.
         * 
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                parent::__construct($core, "respawnboss", "Respawn a boss", RandomUtils::colorMessage("&eUsage: /respawnboss <name: string>"), []);
                $this->setPermission("command.respawnboss");
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

                /** @var Player $sender */
                if(count($args) < 1){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                if(!$this->core->module_loader->bosses->scheduleRespawn(implode(" ", $args), true, false)){
                        $msg = $this->core->getMessage("bosses", "unknown_boss");
                        $sender->sendMessage(RandomUtils::colorMessage($msg));

                        return false;
                }

                return true;
        }
}