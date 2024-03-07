<?php
namespace PrestigeSociety\Forms\FormList\Stones;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\ProtectionStones\Stone;
use PrestigeSociety\UIForms\SimpleForm;
class ListHelpersForm extends FormHandler{
        public function send(Player $player){
                /** @var Stone $stone */
                $stone = $this->getData();

                $ui = new SimpleForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&8&lHELPERS LIST"));
                $content = "";

                if(count($stone->getHelpers()) <= 0){
                        $content = "&7no helpers found.";
                }else{
                        foreach($stone->getHelpers() as $helper => $v){
                                $perms = $v["place"] ? "can build" : "cannot build";
                                $perms .= $v["break"] ? ", can break" : ", cannot break";
                                $perms .= $v["interact"] ? ", can interact" : ", cannot interact";
                                $content .= "&7- " . $helper . " (" . $perms .  ")\n";
                        }
                }

                $ui->setContent(RandomUtils::colorMessage($content));
                $ui->setButton(RandomUtils::colorMessage("&8<- back"));
                $ui->send($player);
        }

        public function handleResponse(Player $player, $formData){
                if($formData === 0){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MANAGE_SINGLE_STONE_ID, $player, $this->getData());
                }
        }
}