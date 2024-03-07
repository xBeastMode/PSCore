<?php
namespace PrestigeSociety\Forms\FormList\Management;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\CustomForm;
class SetAbilityForm extends FormHandler{
        public function send(Player $player){
                $form = new CustomForm($this);

                $item = $this->getData();
                $itemInHand = $player->getInventory()->getItemInHand();
                $itemName = $itemInHand->getName();

                if(!$item->equalsExact($itemInHand)){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "Please do not switch slots while managing item.", $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $form->setTitle(RandomUtils::colorMessage("&l&8SET ABILITY OF $itemName"));

                $abilities = $this->core->module_loader->management->getAvailableAbilities($itemInHand);
                $abilityNames = array_map(function ($ability){
                        return $this->core->module_loader->management->abilityIdToName($ability);
                }, $abilities);

                $abilityMaxDurations = array_map(function ($ability){
                        return RandomUtils::colorMessage(
                            "&l&8» &r&7"
                            . $this->core->module_loader->management->abilityIdToName($ability) . " max units (duration) = "
                            . $this->core->module_loader->management->getAbilityMaxUnits($ability));
                }, $abilities);

                $form->setLabel(implode("\n", $abilityMaxDurations));

                $form->setDropdown(RandomUtils::colorMessage("&7choose ability"), $abilityNames, 0);
                $form->setInput(RandomUtils::colorMessage("&7ability units (duration)"), "ability units", 1);

                $this->setData($itemInHand);
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $abilities = $this->core->module_loader->management->getAvailableAbilities($this->getData());

                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->management->CONFIRM_SET_ABILITY_ID, $player, [
                    $this->getData(),
                    $abilities[$formData[1]],
                    $formData[2]
                ]);
        }
}