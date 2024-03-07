<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\PrestigeSocietyCore;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\Utils\SoundNames;
class OxygenCommand extends CoreCommand{
        /**
         * OxygenCommand constructor.
         * 
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "oxygen", "Allows you to toggle oxygen", "Usage: /oxygen", []);
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

                $sender->setBreathing(!$sender->isBreathing());

                $sender->sendTip(TextFormat::GREEN . ($sender->isBreathing() ? "Oxygen turned on." : "Oxygen turned off."));
                RandomUtils::playSound(SoundNames::SOUND_BUCKET_FILL_WATER, $sender->getLocation());
                return true;
        }
}