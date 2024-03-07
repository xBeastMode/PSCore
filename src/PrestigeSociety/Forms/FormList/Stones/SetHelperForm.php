<?php
namespace PrestigeSociety\Forms\FormList\Stones;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\ProtectionStones\Stone;
use PrestigeSociety\UIForms\CustomForm;
class SetHelperForm extends FormHandler{
        public function send(Player $player){
                $ui = new CustomForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&8&lSET HELPER"));
                $ui->setInput(RandomUtils::colorMessage("&7helper's name"));
                $ui->setToggle(RandomUtils::colorMessage("&7can place"));
                $ui->setToggle(RandomUtils::colorMessage("&7can break"));
                $ui->setToggle(RandomUtils::colorMessage("&7can interact"));
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

                if(strtolower($name) === strtolower($player->getName())){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "Cannot add yourself as helper!", $this->form_id, $this->getData()
                        ]);
                        return;
                }

                $stone->addHelper($name, [
                    'place' => $formData[1],
                    'break' => $formData[2],
                    'interact' => $formData[3],
                ]);

                $this->core->module_loader->protection_stones->saveStone($stone);

                $msg = $this->core->getMessage('stones', 'added_helper');
                $msg = RandomUtils::colorMessage($msg);
                $msg = str_replace(["@name", "@player"], [$stone->getName(), $name], $msg);
                $player->sendMessage($msg);
        }
}