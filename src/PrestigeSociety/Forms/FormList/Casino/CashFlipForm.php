<?php
namespace PrestigeSociety\Forms\FormList\Casino;
use DateTime;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\Player\Data\Settings;
use PrestigeSociety\UIForms\CustomForm;
class CashFlipForm extends FormHandler{
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

                $initial = $this->core->module_configurations->casino["cash_flip"]["initial_percentage"];
                $increase = number_format($this->core->module_configurations->casino["cash_flip"]["increase"]);
                $increaseInt = (int) $this->core->module_configurations->casino["cash_flip"]["increase"];
                $maximumInt = (int) $this->core->module_configurations->casino["cash_flip"]["maximum"];
                $percentage = $this->core->module_configurations->casino["cash_flip"]["percentage_increase"];
                $maximum = $this->core->module_configurations->casino["cash_flip"]["maximum_percentage"];

                $form = new CustomForm($this);
                $form->setTitle(RandomUtils::colorMessage("&8&lCASH FLIP"));
                $content = "===========================\n";
                $content .= "&7Welcome to cash flip!\n\n";
                $content .= "&7The initial chance is $initial percent.\n";
                $content .= "&7Each $$increase you bet, there is $percentage percent\n";
                $content .= "&7more chance of winning. The maximum\n";
                $content .= "&7chance you can get to is $maximum percent.\n";
                $content .= "&7If you win you will get: \n";
                $content .= "&7100 percent cash back + the percent increase you made\n";
                $content .= "===========================\n";
                $form->setLabel(RandomUtils::colorMessage($content));
                $form->setSlider(RandomUtils::colorMessage("&7bet amount"), $increaseInt, $maximumInt, $increaseInt, $increaseInt);
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $bet = $formData[1];

                if(($money = $this->core->module_loader->economy->getCash($player)) < $bet){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            RandomUtils::colorMessage("You do not have enough cash to purchase this\nYou need: {$bet}\nYou have: $money."), $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $maximum = (int) $this->core->module_configurations->casino["cash_flip"]["maximum"];

                $percent = ($bet / $maximum) * 100;
                if($percent > 50) $percent = 50;

                $player->sendTip(RandomUtils::colorMessage("&l&8» &aGOOD LUCK"));
                RandomUtils::playSound("firework.launch", $player);
                $this->core->getScheduler()->scheduleDelayedTask(new class($this->core, $player, $bet, $percent) extends Task{
                        /** @var PrestigeSocietyCore */
                        protected PrestigeSocietyCore $core;
                        /** @var Player */
                        protected Player $player;
                        /** @var int */
                        protected int $bet;
                        /** @var int */
                        protected int $chance;
                        public function __construct(PrestigeSocietyCore $core, Player $player, int $bet, int $chance){
                                $this->core = $core;
                                $this->player = $player;
                                $this->bet = $bet;
                                $this->chance = $chance;
                        }
                        public function onRun(): void{
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->casino->CASH_FLIP_RESULT_ID, $this->player, [$this->bet, $this->chance]);
                        }
                }, 20);
        }
}