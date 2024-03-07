<?php
namespace PrestigeSociety\Bosses\Commands;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class AddBossCommand extends CoreCommand{
        /**
         * AddBossCommand constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                parent::__construct($core, "addboss", "Add a boss spawn", RandomUtils::colorMessage("&eUsage: /addboss <name: string> <size: int> <respawn-period: int> <damage: int> <health: int>"), []);
                $this->setPermission("command.addboss");
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
                if(!$this->testAll($sender)){
                        return false;
                }

                /** @var Player $sender */

                if(count($args) < 5 || !is_numeric($args[1]) || !is_numeric($args[2]) || !is_numeric($args[3]) || !is_numeric($args[4])){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                $name = $args[0];
                $size = (int)$args[1];
                $respawnPeriod = (int)$args[2];
                $damage = (int)$args[3];
                $health = (int)$args[4];

                if($size <= 0 || $damage <= 0 || $health <= 0 || $respawnPeriod <= 0){
                        $msg = $this->core->getMessage("bosses", "invalid_args");
                        $sender->sendMessage(RandomUtils::colorMessage($msg));

                        return false;
                }

                $msg = $this->core->getMessage("bosses", "boss_added");
                $msg = str_replace(["@name", "@damage", "@health"], [$name, $damage, $health], $msg);
                $sender->sendMessage(RandomUtils::colorMessage($msg));

                $this->core->module_loader->bosses->addBossSpawn($name, $sender, $size, $respawnPeriod, $damage, $health);

                return true;
        }
}