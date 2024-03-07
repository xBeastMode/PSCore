<?php
namespace PrestigeSociety\Forms\FormList\Crates;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Crates\Crates;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class BuyCratesForm extends FormHandler{
        public function send(Player $player){
                $crates = [
                    "basic crate" => Crates::TYPE_BASIC_CRATE,
                    "op crate" => Crates::TYPE_OP_CRATE,
                    "exclusive crate" => Crates::TYPE_EXCLUSIVE_CRATE,
                    "weapon crate" => Crates::TYPE_WEAPON_CRATE
                ];

                $form = new SimpleForm($this);
                $form->setTitle(RandomUtils::colorMessage("&8&lBUY CRATES"));
                foreach($crates as $name => $type){
                        $form->setButton(RandomUtils::colorMessage("&8$name\n&7- &8 you have " . $this->core->module_loader->crates->getCrateCount($player, $type) . " &7-"));
                }
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->crates->CONFIRM_PURCHASE_ID, $player, $formData);
        }
}