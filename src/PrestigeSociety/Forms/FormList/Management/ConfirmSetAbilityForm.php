<?php
namespace PrestigeSociety\Forms\FormList\Management;
use pocketmine\item\Item;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\StringUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\Management\StaticManagement;
use PrestigeSociety\UIForms\SimpleForm;
class ConfirmSetAbilityForm extends FormHandler{
        public function send(Player $player){
                $form = new SimpleForm($this);

                /** @var Item $item */
                $item = $this->getData()[0];
                $abilityId = $this->getData()[1];
                $units = $this->getData()[2];

                $maxUnits = $this->core->module_loader->management->getAbilityMaxUnits($abilityId);
                $unitsHad = $this->core->module_loader->management->getItemAbilityDuration($item);

                if(StringUtils::stringToInteger($units) <= 0){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "Units must be above 0.", $this->core->module_loader->management->SET_ABILITY_ID, $item
                        ]);
                        return;
                }

                if($units > $maxUnits){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "Units exceed the max limit: {$maxUnits}.", $this->core->module_loader->management->SET_ABILITY_ID, $item
                        ]);
                        return;
                }

                $itemInHand = $player->getInventory()->getItemInHand();
                $abilityCost = $this->core->module_loader->management->getAbilityCost($abilityId);

                if(!$item->equalsExact($itemInHand)){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "Please do not switch slots while managing item.", $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $form->setTitle(RandomUtils::colorMessage("&l&8CONFIRM SET ABILITY"));

                $content = "===========================\n";
                $content .= "&7units: &f$units\n";
                $content .= "&7cost per unit: &f$abilityCost\n";
                $content .= "&7final cost: &f" . ($abilityCost * $units) . "\n";
                $content .= "&7final units (units in item + purchased units): &f" . $units + $unitsHad . "\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));

                $form->setButton(RandomUtils::colorMessage("&8confirm"));
                $form->setButton(RandomUtils::colorMessage("&8cancel"));

                $form->send($player);
                $this->setData([$itemInHand, $abilityId, $units, $abilityCost]);
        }

        public function handleResponse(Player $player, $formData){
                if($formData === 0){
                        /** @var Item $item */
                        $item = $this->getData()[0];
                        $abilityId = $this->getData()[1];
                        $units = $this->getData()[2];
                        $abilityCost = $this->getData()[3] * $units;

                        $unitsHad = $this->core->module_loader->management->getItemAbilityDuration($item);
                        $abilityName = $this->core->module_loader->management->abilityIdToName($abilityId);

                        if($abilityCost > $this->core->module_loader->economy->getMoney($player)){
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                                    "You do not have enough funds to purchase this.", $this->form_id, MessageForm::METADATA_CLOSE
                                ]);
                                return;
                        }

                        $this->core->module_loader->economy->subtractMoney($player, $abilityCost);
                        $itemResult = $this->core->module_loader->management->setItemAbilityActive($item, $abilityId, $unitsHad + $units);

                        $player->getInventory()->removeItem($player->getInventory()->getItemInHand());
                        $player->getInventory()->addItem($itemResult);

                        $message = $this->core->getMessage("management", "set_ability");
                        $message = str_replace(["@item", "@ability", "@units", "@cost"], [$item->getName(), $abilityName, $units, $abilityCost], $message);
                        $player->sendMessage(RandomUtils::colorMessage($message));
                }
        }
}