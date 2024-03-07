<?php
namespace PrestigeSociety\Bosses\Commands;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class SpawnBossCommand extends CoreCommand{
        /**
         * SpawnBossCommand constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                parent::__construct($core, "spawnboss", "Spawn a boss", RandomUtils::colorMessage("&eUsage: /spawnboss <name: string> <size: int> <damage: int> <health: int>"), []);
                $this->setPermission("command.spawnboss");
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
                if(count($args) < 4 || !is_numeric($args[1]) || !is_numeric($args[2]) || !is_numeric($args[3])){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                $name = $args[0];
                $size = (int)$args[1];
                $damage = (int)$args[2];
                $health = (int)$args[2];

                if($size <= 0 || $damage <= 0){
                        $msg = $this->core->getMessage("bosses", "invalid_args");
                        $sender->sendMessage(RandomUtils::colorMessage($msg));

                        return false;
                }

                $msg = $this->core->getMessage("bosses", "boss_spawned");
                $msg = str_replace("@name", $name, $msg);
                $sender->sendMessage(RandomUtils::colorMessage($msg));

                $this->core->module_loader->bosses->spawnBoss($name, $sender, $size, $damage, $health);

                return true;
        }
}