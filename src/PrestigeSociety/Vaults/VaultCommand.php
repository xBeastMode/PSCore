<?php
namespace PrestigeSociety\Vaults;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use pocketmine\player\Player;
class VaultCommand extends CoreCommand{
        /**
         * VaultCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "pv", "Private vaults command!", RandomUtils::colorMessage("&eUsage: /pv <number> [player]"), ["vault"]);
        }

        /**
         * @param CommandSender $sender
         * @param string $commandLabel
         * @param string[] $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPlayer($sender)){
                        return false;
                }

                /** @var Player $sender */

                if(count($args) < 1){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                if(intval($args[0]) < 1){
                        $messagePlayer = $this->core->getMessage("vaults", "invalid_vault_number");
                        $sender->sendMessage(RandomUtils::colorMessage($messagePlayer));

                        return false;
                }

                $number = intval($args[0]);
                $username = $args[1] ?? null;

                if($username !== null){
                        if(!$this->testCustomPermission($sender, "pv.admin")){
                                return false;
                        }
                        $this->core->module_loader->vaults->openWindow($sender, $number, $username);
                        return true;
                }


                if(PermissionManager::getInstance()->getPermission("pv." . $number) === null){
                        PermissionManager::getInstance()->addPermission(new Permission("pv." . $number, "", [RandomUtils::getOperatorPermission()]));
                }

                if($sender->hasPermission("pv." . $number) || $sender->hasPermission("pv.infinite")){
                        $this->core->module_loader->vaults->openWindow($sender, $number, $sender->getName());
                }else{
                        $messagePlayer = $this->core->getMessage("vaults", "no_vault_permission");
                        $sender->sendMessage(RandomUtils::colorMessage($messagePlayer));

                        return false;
                }
                return true;
        }
}