<?php
namespace PrestigeSociety\Forms\FormList\Crates;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Crates\Crates;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\SimpleForm;
class ConfirmPurchaseForm extends FormHandler{
        public function send(Player $player){
                $form = new SimpleForm($this);
                $form->setTitle(RandomUtils::colorMessage("&8&lCONFIRM PURCHASE"));

                $crates = [
                    Crates::TYPE_BASIC_CRATE,
                    Crates::TYPE_OP_CRATE,
                    Crates::TYPE_EXCLUSIVE_CRATE,
                    Crates::TYPE_WEAPON_CRATE
                ];

                $formData = $this->getData();

                $crate = $crates[$formData];
                $crate = $this->core->module_configurations->crates[$crate];

                $name = $crate["name"];
                $cost = (int) $crate["cost"];
                $rewards = $crate["rewards"];

                $content = "===========================\n";
                $content .= "&7crate type: &f$name\n";
                $content .= "&7total cost: &f$cost\n";
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
                            Crates::TYPE_WEAPON_CRATE
                        ];

                        $formData = $this->getData();

                        $crate = $crates[$formData];
                        $crate = $this->core->module_configurations->crates[$crate];

                        $name = $crate["name"];
                        $cost = (int) $crate["cost"];

                        if(($money = $this->core->module_loader->economy->getMoney($player)) < $cost){
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                                    RandomUtils::colorMessage("You don't have enough cash to purchase this\nYou need: {$cost}\nYou have: $money."), $this->form_id, MessageForm::METADATA_CLOSE
                                ]);
                                return;
                        }

                        $this->core->module_loader->crates->addCrateCount($player, $crates[$formData]);
                        $this->core->module_loader->economy->subtractMoney($player, $cost);

                        $message = $this->core->getMessage("crates", "buy_crate");
                        $message = str_replace(["@cost", "@crate"], [$cost, $name], $message);
                        $player->sendMessage(RandomUtils::colorMessage($message));
                }
        }
}