<?php
namespace PrestigeSociety\Forms\FormList\Casino;
use DateTime;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\Player\Data\Settings;
use PrestigeSociety\UIForms\SimpleForm;
class SlotMachinesForm extends FormHandler{
        public function send(Player $player){
                $settings = $this->core->module_loader->player_data->getFreshPlayerSettings($player);
                $timesWon = $settings->get(Settings::CASINO_WINS, 0);

                if(!$player->hasPermission("casino.playtime.bypass") && $timesWon >= $this->core->module_configurations->casino["max_casino_wins"]){
                        if(!$settings->exists(Settings::CASINO_NEXT_PLAYTIME)){
                                $nextPlayTime = $this->core->module_configurations->casino["next_play_time"];
                                $nextPlayTime = time() + $nextPlayTime;

                                $settings->set(Settings::CASINO_NEXT_PLAYTIME, $nextPlayTime);
                                $settings->save();
                        }else{
                                $nextPlayTime = $settings->get(Settings::CASINO_NEXT_PLAYTIME);
                        }

                        $timeDiff = (new DateTime())->setTimestamp($nextPlayTime)->diff(new DateTime());
                        $timeLeft = $nextPlayTime - time();

                        if($timeLeft > 0){
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                                    RandomUtils::colorMessage("You have won too many times today, try again in: {$timeDiff->d} days, {$timeDiff->h} hours, {$timeDiff->i} minutes, {$timeDiff->s} seconds"), $this->form_id, MessageForm::METADATA_CLOSE
                                ]);
                                return;
                        }

                        $settings->delete(Settings::CASINO_WINS);
                        $settings->delete(Settings::CASINO_NEXT_PLAYTIME);

                        $settings->save();
                }

                $form = new SimpleForm($this);
                $form->setTitle(RandomUtils::colorMessage("&8&lSLOT MACHINES"));

                foreach($this->core->module_configurations->casino["slot_machines"] as $machine){
                        $form->setButton(RandomUtils::colorMessage("&8{$machine["name"]}\n&7- &8{$machine["description"]} &7-"));
                }

                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->casino->SLOT_MACHINE_SPIN_ID, $player, $formData);
        }
}