<?php
namespace PrestigeSociety\Forms\FormList\Casino;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\SimpleForm;
class SlotMachineSpinForm extends FormHandler{
        public function send(Player $player){
                $machine = $this->core->module_configurations->casino["slot_machines"][$this->getData()];

                $form = new SimpleForm($this);
                $form->setTitle(RandomUtils::colorMessage("&8&lSPIN " . strtoupper($machine["name"]) . " SLOT MACHINE"));

                $content = "===========================\n";
                $content .= "&7machine cost: &f{$machine["cost"]}\n";
                $content .= "&7possible rewards: &f{$machine["rewards"]}\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));
                $form->setButton(RandomUtils::colorMessage("&8spin"));
                $form->setButton(RandomUtils::colorMessage("&8cancel"));
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $machine = $this->core->module_configurations->casino["slot_machines"][$this->getData()];

                if($formData === 0){
                        if(($money = $this->core->module_loader->economy->getCash($player)) < $machine["cost"]){
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                                    RandomUtils::colorMessage("You do not have enough cash to purchase this\nYou need: {$machine["cost"]}\nYou have: $money."), $this->form_id, MessageForm::METADATA_CLOSE
                                ]);
                                return;
                        }

                        $this->core->module_loader->economy->deposit($player, $machine["cost"], false);
                        $this->core->module_loader->casino->spin($player, $this->getData());
                }else{
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->casino->SLOT_MACHINES_ID, $player);
                }
        }
}