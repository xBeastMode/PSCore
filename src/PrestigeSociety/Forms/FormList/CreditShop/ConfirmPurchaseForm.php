<?php
namespace PrestigeSociety\Forms\FormList\CreditShop;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\ConsoleUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Player\Data\Settings;
use PrestigeSociety\UIForms\SimpleForm;
class ConfirmPurchaseForm extends FormHandler{
        public function send(Player $player){
                $data = $this->getData();
                $form = new SimpleForm($this);

                $form->setTitle(RandomUtils::colorMessage("&8&lCONFIRM PURCHASE"));
                $credits = $this->core->module_loader->credits->getCredits($player);
                $form->setContent(RandomUtils::colorMessage(str_replace(["@cost", "@credits"], [$data["cost"], $credits], $data["description"])));

                $form->setButton(RandomUtils::colorMessage("&8yes"));
                $form->setButton(RandomUtils::colorMessage("&8no"));

                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $data = $this->getData();

                if($formData === 0){
                        $cost = $data["cost"];
                        $credits = $this->core->module_loader->credits->getCredits($player);

                        if($credits < $cost){
                                $player->sendMessage(RandomUtils::colorMessage($this->core->getMessage("credit_shop", "non_sufficient_funds")));
                                return;
                        }

                        $settings = $this->core->module_loader->player_data->getPlayerSettings($player);

                        $v = $settings->get(Settings::SETTING_ALREADY_PURCHASED);

                        $v[] = $data["name"];

                        $settings->set(Settings::SETTING_ALREADY_PURCHASED, $v);
                        $settings->save();

                        $this->core->module_loader->credits->subtractCredits($player, $cost);
                        foreach($data["commands"] as $command){
                                ConsoleUtils::dispatchCommandAsConsole(str_replace("@player", $player->getName(), $command));
                        }

                        $msg = $this->core->getMessage("credit_shop", "item_purchased");
                        $msg = str_replace(["@name", "@cost"], [$data["name"], $cost], $msg);
                        $player->sendMessage(RandomUtils::colorMessage($msg));
                }
        }
}