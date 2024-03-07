<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class StackStickCommand extends CoreCommand{
        /**
         * StackStickCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "stackstick", "Stack another player on you!", RandomUtils::colorMessage("&e/stackstick"), []);
                $this->setPermission("command.stackstick");
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

                $stick = VanillaItems::STICK();
                $stick->getNamedTag()->setByte("stack_stick", 1);

                $stick->setCustomName(RandomUtils::colorMessage("§r§l§cS§6t§ea§ac§bk§9 §5S§ct§6i§ec§ak"));

                if(!$sender->getInventory()->canAddItem($stick)){
                        $sender->sendPopup(RandomUtils::colorMessage("&c[!] You inventory is too full."));
                        return false;
                }

                $sender->getInventory()->addItem($stick);
                $sender->sendPopup(RandomUtils::colorMessage("&l&8» §r§l§cS§6t§ea§ac§bk§9 §5S§ct§6i§ec§ak &r&areceived."));

                return true;
        }
}