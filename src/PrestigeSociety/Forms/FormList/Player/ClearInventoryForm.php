<?php
namespace PrestigeSociety\Forms\FormList\Player;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\CustomForm;
class ClearInventoryForm extends FormHandler{
        public function send(Player $player){
                $ui = new CustomForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&8&lDELETE INVENTORY"));
                $ui->setDropdown(RandomUtils::colorMessage("&7clear type"), ["armor", "inventory", "armor and inventory"], 0);
                $ui->setToggle(RandomUtils::colorMessage("&7confirm deletion"), false);
                $ui->send($player);
        }

        public function handleResponse(Player $player, $formData){
                if($formData[1]){
                        if($formData[0] === 0 || $formData[0] === 2){
                                $player->getArmorInventory()->clearAll();
                        }
                        if($formData[0] === 1 || $formData[0] === 2){
                                $player->getInventory()->clearAll();
                        }

                        $player->sendPopup(RandomUtils::colorMessage("&l&8» &6INVENTORY CLEARED"));
                        RandomUtils::playSound("random.levelup", $player, 1000, 1, false);
                }
        }
}