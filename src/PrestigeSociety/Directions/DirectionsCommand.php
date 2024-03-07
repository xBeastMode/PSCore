<?php
namespace PrestigeSociety\Directions;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class DirectionsCommand extends CoreCommand{
        /**
         * DirectionsCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.directions");
                parent::__construct($plugin, "directions", "Get directions to a certain place", RandomUtils::colorMessage("&eUsage: /directions <set|remove> <name>"), []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(count($args) < 2){
                        if(!$this->testPlayer($sender)){
                                return false;
                        }

                        /** @var Player $sender */
                        $options = ["check_permissions" => false, "message" => $this->core->getMessage("command_lock", "directions")];
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->directions->DIRECTION_ID, $sender, [], $options);
                }else{
                        if(!$this->testAll($sender)){
                                return false;
                        }

                        /** @var Player $sender */

                        $type = $args[0];
                        $name = $args[1];

                        if(!in_array(strtolower($type), ["set", "remove"])){
                                $sender->sendMessage($this->getUsage());
                                return false;
                        }

                        if($type === "set"){
                                $this->core->module_loader->directions->addDirection($name, $sender->getPosition());
                                $sender->sendMessage(RandomUtils::colorMessage("&aSuccessfully saved direction $name where you arre standing."));
                        }else{
                                $this->core->module_loader->directions->removeDirection($name);
                                $sender->sendMessage(RandomUtils::colorMessage("&aSuccessfully remove direction $name."));
                        }
                }

                return true;
        }

}