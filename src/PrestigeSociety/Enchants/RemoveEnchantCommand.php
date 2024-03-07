<?php

namespace PrestigeSociety\Enchants;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class RemoveEnchantCommand extends CoreCommand{
        /**
         * RemoveEnchantCommand constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                parent::__construct($core, "removenchant", "Remove an enchant here!", RandomUtils::colorMessage("&eUsage: /rmench"), ["rmench"]);
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
                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->enchants->REMOVE_ENCHANT_ID, $sender);
                return true;
        }
}