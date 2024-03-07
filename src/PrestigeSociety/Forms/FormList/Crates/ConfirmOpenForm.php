<?php
namespace PrestigeSociety\Forms\FormList\Crates;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Crates\Crates;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\SimpleForm;
class ConfirmOpenForm extends FormHandler{
        public function send(Player $player){
                $form = new SimpleForm($this);
                $form->setTitle(RandomUtils::colorMessage("&8&lCONFIRM OPEN"));

                $crates = [
                    Crates::TYPE_BASIC_CRATE,
                    Crates::TYPE_OP_CRATE,
                    Crates::TYPE_EXCLUSIVE_CRATE,
                    Crates::TYPE_VOTE_CRATE,
                    Crates::TYPE_WEAPON_CRATE
                ];

                $formData = $this->getData();

                $crate = $crates[$formData];
                $crate = $this->core->module_configurations->crates[$crate];

                $name = $crate["name"];
                $rewards = $crate["rewards"];

                $content = "===========================\n";
                $content .= "&7crate type: &f$name\n";
                $content .= "&7possible rewards: &f$rewards\n";
                $content .= "===========================\n";

                $form->setContent(RandomUtils::colorMessage($content));

                $form->setButton(RandomUtils::colorMessage("&8yes"));
                $form->setButton(RandomUtils::colorMessage("&8no"));
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                if($formData === 0){
                        $crates = [
                            Crates::TYPE_BASIC_CRATE,
                            Crates::TYPE_OP_CRATE,
                            Crates::TYPE_EXCLUSIVE_CRATE,
                            Crates::TYPE_VOTE_CRATE,
                            Crates::TYPE_WEAPON_CRATE
                        ];

                        $formData = $this->getData();
                        $crate = $crates[$formData];

                        if($this->core->module_loader->crates->getCrateCount($player, $crate) <= 0){
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                                    RandomUtils::colorMessage("You don't have any crates of this type to open."), $this->form_id, MessageForm::METADATA_CLOSE
                                ]);
                                return;
                        }

                        $this->core->module_loader->crates->openCrate($player, $crate);
                }
        }
}