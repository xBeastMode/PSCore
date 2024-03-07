<?php
namespace PrestigeSociety\Forms\FormList\PowerUps;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\PowerUps\PowerUps;
use PrestigeSociety\UIForms\SimpleForm;
class BuyPowerUpForm extends FormHandler{
        public function send(Player $player){
                $form = new SimpleForm($this);
                $form->setTitle(RandomUtils::colorMessage("&8&lAVAILABLE POWER-UPS"));

                $active = [
                    "double mine booster" => "each block you mine doubles",
                    "triple mine booster" => "each block you mine triples",
                    "boss reward booster" => "your boss rewards are doubled",
                    "flight power up" => "you are allowed to use /fly",
                    //"crate key drop booster" => "boost by 1000 percent"
                ];

                foreach($active as $i => $v){
                        $form->setButton(RandomUtils::colorMessage("&8$i\n&7- &8$v &7-"));
                }

                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $powerUps = [
                    [PowerUps::POWER_UP_MINING, "double mine booster"],
                    [PowerUps::POWER_UP_MINING_TRIPLE, "triple mine booster"],
                    [PowerUps::POWER_UP_BOSS, "boss reward booster"],
                    [PowerUps::POWER_UP_FLIGHT, "flight power up"],
                    //[PowerUps::POWER_UP_KEY_DROP, "crate key drop booster"]
                ];

                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->power_ups->CONFIRM_PURCHASE_ID, $player, $powerUps[$formData]);
        }
}