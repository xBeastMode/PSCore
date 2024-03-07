<?php
namespace PrestigeSociety\Forms\FormList\Casino;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Player\Data\Settings;
use PrestigeSociety\UIForms\SimpleForm;
class CashFlipResultForm extends FormHandler{
        public function send(Player $player){
                $data = $this->getData();

                $form = new SimpleForm($this);

                $cash = $data[0] * ((100 + $data[1]) / 100);
                $percent = 100 + $data[1];
                if(RandomUtils::randomFloat(1, 100) <= $data[1]){
                        RandomUtils::playSound("random.levelup", $player);

                        $this->core->module_loader->economy->withdraw($player, $cash);
                        $form->setTitle(RandomUtils::colorMessage("&8&lCONGRATULATIONS, YOU WIN!"));
                        $content = "===========================\n";
                        $content .= "&7 +$" . number_format($cash) . "\n";
                        $content .= "&7 +$percent percent cash back\n";
                        $content .= "===========================\n";

                        $message = $this->core->getMessage("casino", "won_cash_flip");
                        $message = str_replace(["@player", "@money"], [$player->getName(), $cash], $message);
                        $this->core->module_loader->casino->broadcastMessage(RandomUtils::colorMessage($message));

                        $settings = $this->core->module_loader->player_data->getFreshPlayerSettings($player);

                        $timesWon = $settings->get(Settings::CASINO_WINS, 0);
                        $settings->set(Settings::CASINO_WINS, $timesWon + 1);

                        $settings->save();
                }else{
                        RandomUtils::playSound("mob.cat.meow", $player, 500, 0.6);

                        $this->core->module_loader->economy->deposit($player, $data[0], false);
                        $form->setTitle(RandomUtils::colorMessage("&8&lSORRY, YOU HAVE LOST"));
                        $content = "===========================\n";
                        $content .= "&7 -$" . number_format($data[0]) . "\n";
                        $content .= "===========================\n";
                }
                $form->setContent(RandomUtils::colorMessage($content));
                $form->setButton(RandomUtils::colorMessage("&8play again!"));
                $form->setButton(RandomUtils::colorMessage("&8quit"));
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                if($formData === 0){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->casino->CASH_FLIP_ID, $player);
                }
        }
}