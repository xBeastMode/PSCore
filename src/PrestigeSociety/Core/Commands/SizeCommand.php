<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormList\Player\SizeForm;
use PrestigeSociety\Forms\FormManager;
class SizeCommand extends CoreCommand{
        /** @var int */
        protected int $SIZE_FORM = 0;

        /**
         * SizeCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "size", "Change your size", RandomUtils::colorMessage("&e/size"), ["scale"]);
                $this->setPermission("command.size");

                $this->SIZE_FORM = FormManager::getNextFormId();
                $plugin->module_loader->form_manager->registerHandler($this->SIZE_FORM, SizeForm::class);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testAll($sender)){
                        return false;
                }

                /** @var Player $sender */
                $this->core->module_loader->form_manager->sendForm($this->SIZE_FORM, $sender);
                return true;
        }
}