<?php
namespace PrestigeSociety\Forms\FormList\PowerUps;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Powerups\PowerUps;
use PrestigeSociety\UIForms\SimpleForm;
class ActivatePowerUpsForm extends FormHandler{
        public function send(Player $player){
                $powerups = $this->core->module_loader->power_ups->getPlayerPowerUps($player);

                $form = new SimpleForm($this);
                $form->setTitle(RandomUtils::colorMessage("&8&lACTIVATE POWER-UPS"));

                $active = [
                    "double mine booster" => PowerUps::POWER_UP_MINING,
                    "triple mine booster" => PowerUps::POWER_UP_MINING_TRIPLE,
                    "boss reward booster" => PowerUps::POWER_UP_BOSS,
                    "flight power up" => PowerUps::POWER_UP_FLIGHT,
                    //"crate key drop booster" => PowerUps::POWER_UP_KEY_DROP
                ];

                foreach($active as $n => $v){
                        $b = $this->core->module_loader->power_ups->isPowerUpActive($player, $v);
                        $c = count($powerups[$v] ?? []);
                        $t = round($this->core->module_loader->power_ups->getActivePowerUpTimeLeft($player, $v), 1);

                        $form->setButton(RandomUtils::colorMessage("&8$n ($c)" . ($b ? "\n&7- &cACTIVE &8($t hours left) &7-" : "")));
                }

                $form->send($player);
                $this->setData($powerups);
        }

        public function handleResponse(Player $player, $formData){
                $powerups = $this->getData();

                $active = [
                    ["double mine booster", PowerUps::POWER_UP_MINING, count($powerups[PowerUps::POWER_UP_MINING] ?? [])],
                    ["triple mine booster", PowerUps::POWER_UP_MINING_TRIPLE, count($powerups[PowerUps::POWER_UP_MINING_TRIPLE] ?? [])],
                    ["boss reward booster", PowerUps::POWER_UP_BOSS, count($powerups[PowerUps::POWER_UP_BOSS] ?? [])],
                    ["flight power up", PowerUps::POWER_UP_FLIGHT, count($powerups[PowerUps::POWER_UP_FLIGHT] ?? [])],
                    //["crate key drop booster", PowerUps::POWER_UP_KEY_DROP, count($powerups[PowerUps::POWER_UP_KEY_DROP] ?? [])]
                ];

                if($this->core->module_loader->power_ups->isPowerUpActive($player, $active[$formData][1])){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            RandomUtils::colorMessage("You already have a {$active[$formData][0]} active."), $this->form_id, []
                        ]);
                        return;
                }

                if($active[$formData][2] <= 0){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            RandomUtils::colorMessage("You have no {$active[$formData][0]}s."), $this->form_id, []
                        ]);
                        return;
                }

                $this->core->module_loader->power_ups->setPowerUpActive($player, $active[$formData][1]);
                $t = round($this->core->module_loader->power_ups->getActivePowerUpTimeLeft($player, $active[$formData][1]), 1);

                $message = $this->core->getMessage('power_ups', 'activated');
                $message = str_replace(["@name", "@time"], [$active[$formData][0], $t], $message);
                $player->sendMessage(RandomUtils::colorMessage($message));
        }
}