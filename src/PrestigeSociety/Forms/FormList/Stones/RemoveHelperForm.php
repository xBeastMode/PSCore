<?php
namespace PrestigeSociety\Forms\FormList\Stones;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\ProtectionStones\Stone;
use PrestigeSociety\UIForms\CustomForm;
class RemoveHelperForm extends FormHandler{
        public function send(Player $player){
                $ui = new CustomForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&8&lREMOVE HELPER"));
                $ui->setInput(RandomUtils::colorMessage("&7helper's name"));
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

                $stone->removeHelper($name);
                $this->core->module_loader->protection_stones->saveStone($stone);

                $msg = $this->core->getMessage("stones", "removed_helper");
                $msg = RandomUtils::colorMessage($msg);
                $msg = str_replace(["@name", "@player"], [$stone->getName(), $name], $msg);
                $player->sendMessage($msg);
        }
}