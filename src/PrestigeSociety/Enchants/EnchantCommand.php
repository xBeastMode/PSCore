<?php

namespace PrestigeSociety\Enchants;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class EnchantCommand extends CoreCommand{
        /**
         * EnchantCommand constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                parent::__construct($core, "buyenchant", "Want an enchantment? Buy it here!", RandomUtils::colorMessage("&eUsage: /enchant"), ["buyenchantment", "buyench"]);
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
                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->enchants->ENCHANTS_ID, $sender);
                return true;
        }

}