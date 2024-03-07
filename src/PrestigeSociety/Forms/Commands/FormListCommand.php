<?php
namespace PrestigeSociety\Forms\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\StringUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
class FormListCommand extends CoreCommand{
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.formlist");
                parent::__construct($plugin, "formlist", "List all form ids", "Usage: /formlist", []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPermissionAndPlayer($sender, $this->getPermission())){
                        return false;
                }

                /** @var Player $sender */

                $mod = $this->core->module_loader->form_manager;
                $form = $mod->getFastSimpleForm($sender, fn() => null);

                $content = "";
                $categoryOutput = [];

                $form->setTitle(RandomUtils::colorMessage("&l&8FORM LIST (DEBUGGING)"));

                foreach($mod->getHandlers() as $id => $handler){
                        $handler = explode("\\", $handler);

                        $formAlias = array_pop($handler);
                        $formCategory = array_pop($handler);

                        if(!isset($categoryOutput[$formCategory])) $categoryOutput[$formCategory] = "";
                        $categoryOutput[$formCategory] .= "  &7◾ $id &8= &7$formAlias\n";
                }

                foreach($categoryOutput as $category => $output){
                        $content .= "&l&7● $category&r\n$output\n";
                }

                $form->setContent(RandomUtils::colorMessage($content));
                $form->setButton(RandomUtils::colorMessage("&l&8close"));
                $form->send($sender);

                return true;
        }
}