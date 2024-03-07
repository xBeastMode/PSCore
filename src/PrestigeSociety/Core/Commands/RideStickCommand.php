<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class RideStickCommand extends CoreCommand{
        /**
         * RideStickCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "ridestick", "Ride another player!", RandomUtils::colorMessage("&eUsage: /ridestick"), []);
                $this->setPermission("command.ridestick");
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param array         $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testAll($sender)){
                        return false;
                }

                /** @var Player $sender */

                $stick = VanillaItems::STICK();
                $stick->getNamedTag()->setByte("ride_stick", 1);

                $stick->setCustomName(RandomUtils::colorMessage("§r§l§cR§6i§ed§ai§bn§9g§5 §cS§6t§ei§ac§bk"));

                if(!$sender->getInventory()->canAddItem($stick)){
                        $sender->sendPopup(RandomUtils::colorMessage("&l&4[!] &cYou inventory is too full."));
                        return false;
                }

                $sender->getInventory()->addItem($stick);
                $sender->sendPopup(RandomUtils::colorMessage("&l&8» §cR§6i§ed§ai§bn§9g§5 §cS§6t§ei§ac§bk &r&areceived."));

                return true;
        }
}