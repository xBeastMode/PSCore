<?php
namespace PrestigeSociety\Forms\FormList\Stones;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\ProtectionStones\Stone;
use PrestigeSociety\UIForms\SimpleForm;
class ManageSingleStoneForm extends FormHandler{
        public function send(Player $player){
                /** @var Stone $stone */
                $stone = $this->getData();

                $ui = new SimpleForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&8&lMANAGE " . $stone->getName() . ""));
                $ui->setButton(RandomUtils::colorMessage("&8<- back"));
                $ui->setButton(RandomUtils::colorMessage("&7- &8transfer ownership &7-"));
                $ui->setButton(RandomUtils::colorMessage("&7- &8list helpers &7-"));
                $ui->setButton(RandomUtils::colorMessage("&7- &8set helper &7-"));
                $ui->setButton(RandomUtils::colorMessage("&7- &8remove helper &7-"));
                $ui->setButton(RandomUtils::colorMessage("&7- &8delete &7-"));
                $ui->send($player);

        }

        public function handleResponse(Player $player, $formData){
                switch($formData){
                        case 0:
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MANAGE_STONES_ID, $player);
                                break;
                        case 1:
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->TRANSFER_OWNERSHIP, $player, $this->getData());
                                break;
                        case 2:
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->LIST_HELPERS_ID, $player, $this->getData());
                                break;
                        case 3:
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->SET_HELPER_ID, $player, $this->getData());
                                break;
                        case 4:
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->REMOVE_HELPER_ID, $player, $this->getData());
                                break;
                        case 5:
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->CONFIRM_DELETION_ID, $player, $this->getData());
                                break;
                }
        }
}