<?php
namespace PrestigeSociety\Forms\FormList\Stones;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\ProtectionStones\Stone;
use PrestigeSociety\UIForms\CustomForm;
class EditHelperForm extends FormHandler{
        public function send(Player $player){
                $ui = new CustomForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&8&lEDIT HELPER"));
                $ui->setInput(RandomUtils::colorMessage("&8helper's name"));
                $ui->setToggle(RandomUtils::colorMessage("&8can build"));
                $ui->setToggle(RandomUtils::colorMessage("&8can place"));
                $ui->send($player);
        }

        public function handleResponse(Player $player, $formData){
                /** @var Stone $stone */
                $stone = $this->getData();

                $name = $formData[0];

                if(strlen($name) <= 0){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "That name is too short!", $this->form_id, $this->getData()
                        ]);
                        return;
                }

                if(!$formData[1]){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "You must confirm that you want to transfer ownership.", $this->form_id, $this->getData()
                        ]);
                        return;
                }

                $this->core->module_loader->protection_stones->deleteStone($stone);

                $stone->setOwner($name);
                $this->core->module_loader->protection_stones->saveStone($stone);

                $msg = $this->core->getMessage("stones", "ownership_transferred");
                $msg = RandomUtils::colorMessage($msg);
                $msg = str_replace(["@name", "@player"], [$stone->getName(), $name], $msg);
                $player->sendMessage($msg);
        }
}