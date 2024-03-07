<?php
namespace PrestigeSociety\Forms\Commands;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\StringUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
class OpenFormCommand extends CoreCommand{
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.openform");
                parent::__construct($plugin, "openform", "Open a form for a player", "Usage: /openform <player> <form alias> [extraData]", []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPermission($sender)){
                        return false;
                }

                if(count($args) < 2){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                $player = array_shift($args);
                $alias = array_shift($args);

                $player = $sender->getServer()->getPlayerByPrefix($player);
                if($player === null){
                        $sender->sendMessage(RandomUtils::colorMessage("&cThat player is offline."));
                        return false;
                }

                $mod = $this->core->module_loader->form_manager;
                $form_id = $mod->filterFormHandlerIds($alias);
                if(count($form_id) <= 0 && !($mod->formExists($alias))){
                        $sender->sendMessage(RandomUtils::colorMessage("&cNo forms found with alias $alias"));
                        return false;
                }

                $extraData = $full_extra_data = implode(" ", $args);

                if(RandomUtils::isJson($full_extra_data)){
                        $extraData = json_decode($full_extra_data, true);
                }elseif(StringUtils::checkIsNumber($full_extra_data)){
                        $extraData = $full_extra_data + 0;
                }

                $this->core->module_loader->form_manager->sendForm($form_id[0] ?? $alias, $player, $extraData, ["check_permissions" => false]);
                return true;
        }
}