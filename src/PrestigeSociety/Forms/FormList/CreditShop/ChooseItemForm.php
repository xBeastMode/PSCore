<?php
namespace PrestigeSociety\Forms\FormList\CreditShop;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Player\Data\Settings;
use PrestigeSociety\UIForms\SimpleForm;
class ChooseItemForm extends FormHandler{
        public function send(Player $player){
                $data = $this->core->module_configurations->credit_shop;

                $form = new SimpleForm($this);

                $form->setTitle(RandomUtils::colorMessage("&8&lCREDIT SHOP"));
                $credits = $this->core->module_loader->credits->getCredits($player);
                $form->setContent(RandomUtils::colorMessage("&7Your credits: &f$credits"));

                $unlocked = $this->core->module_loader->player_data->getPlayerSettings($player)->get(Settings::SETTING_ALREADY_PURCHASED) ?? [];

                foreach($data as $datum){
                        $alreadyUnlocked = in_array($datum["name"], $unlocked);
                        $form->setButton(RandomUtils::colorMessage("&8{$datum["name"]}\n&8{$datum["cost"]} credits" . (($alreadyUnlocked && !$datum["rebuy"]) || $player->hasPermission($datum["permission"]) ? "&r - &4&lUNLOCKED" : "")));
                }

                $form->send($player);
                $this->setData($data);
        }

        public function handleResponse(Player $player, $formData){
                $data = $this->getData()[$formData];

                $unlocked = $this->core->module_loader->player_data->getPlayerSettings($player)->get(Settings::SETTING_ALREADY_PURCHASED) ?? [];
                $alreadyUnlocked = in_array($data["name"], $unlocked);

                if(($alreadyUnlocked && !$data["rebuy"]) || $player->hasPermission($data["permission"])){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            RandomUtils::colorMessage("&7You already have this item unlocked."), $this->form_id, []
                        ]);
                        return;
                }

                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->credit_shop->CONFIRM_PURCHASE_ID, $player, $data);
        }
}