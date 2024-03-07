<?php
namespace PrestigeSociety\Forms\FormList\Player;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class SizeForm extends FormHandler{
        public function send(Player $player){
                $ui = new SimpleForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&8&lCHANGE SIZE"));
                $ui->setButton(RandomUtils::colorMessage("&8micro\n&7- &810 percent normal size &7-"));
                $ui->setButton(RandomUtils::colorMessage("&8tiny\n&7- &850 percent normal size &7-"));
                $ui->setButton(RandomUtils::colorMessage("&8normal\n&7- &8normal size &7-"));
                $ui->setButton(RandomUtils::colorMessage("&8giant\n&7- &8200 percent normal size &7-"));
                $ui->setButton(RandomUtils::colorMessage("&8godzilla\n&7- &8400 percent normal size &7-"));
                $ui->send($player);
        }

        public function handleResponse(Player $player, $formData){
                switch($formData){
                        case 0:
                                $player->setScale(0.1);
                                $player->sendPopup(RandomUtils::colorMessage("&l&8» &echanged size to: &6micro"));
                                break;
                        case 1:
                                $player->setScale(0.5);
                                $player->sendPopup(RandomUtils::colorMessage("&l&8» &echanged size to: &6tiny"));
                                break;
                        case 2:
                                $player->setScale(1);
                                $player->sendPopup(RandomUtils::colorMessage("&l&8» &echanged size to: &6normal"));
                                break;
                        case 3:
                                $player->setScale(2);
                                $player->sendPopup(RandomUtils::colorMessage("&l&8» &echanged size to: &6giant"));
                                break;
                        case 4:
                                $player->setScale(4);
                                $player->sendPopup(RandomUtils::colorMessage("&l&8» &echanged size to: &6godzilla"));
                                break;
                }
        }
}