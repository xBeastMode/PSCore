<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\Utils\SoundNames;
class EatCommand extends CoreCommand{
        /**
         * EatCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.eat");
                parent::__construct($plugin, "eat", "Hungry? Fill your hunger bar!", RandomUtils::colorMessage("&e/eat"), ["food", "feed"]);
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

                $hungerManager = $sender->getHungerManager();
                if($hungerManager->getFood() < $hungerManager->getMaxFood()){
                        $hungerManager->setFood($hungerManager->getMaxFood());
                        $hungerManager->setSaturation(20);

                        $sender->sendTip(RandomUtils::colorMessage("&l&8» &aHUNGER SET FULL"));

                        RandomUtils::playSound(SoundNames::SOUND_RANDOM_EAT, $sender->getLocation());
                        RandomUtils::playSound(SoundNames::SOUND_RANDOM_BURP, $sender->getLocation());
                }

                return true;
        }
}