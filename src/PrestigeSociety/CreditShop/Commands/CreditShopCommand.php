<?php
namespace PrestigeSociety\CreditShop\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
class CreditShopCommand extends CoreCommand{
        /**
         * CreditShopCommand constructor.
         * 
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "creditshop", "Shop for items with credits!", "Usage: /creditshop", ["cshop"]);
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
                $options = ["check_permissions" => true, "message" => $this->core->getMessage("command_lock", "credit_shop")];
                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->credit_shop->CHOOSE_ITEM_ID, $sender, [], $options);
                return true;
        }
}