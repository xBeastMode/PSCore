<?php
namespace PrestigeSociety\Forms\FormList\Player;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\Utils\StringUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class ProfileForm extends FormHandler{
        public function send(Player $player){
                $name = $this->getData();

                $mods = $this->core->module_loader;
                $rank = $mods->ranks->getRank($name);

                $ui = new SimpleForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&l&8" . $name . "'s PROFILE"));
                $ui->setButton(RandomUtils::colorMessage("&8close"));

                if($rank !== null){
                        $kills = $mods->levels->getKills($name);
                        $deaths = $mods->levels->getDeaths($name);
                        $level = $mods->levels->getLevel($name);
                        $placed = $mods->levels->getBlocksPlaced($name) + $mods->levels->getTempBlockPlace($name);
                        $broken = $mods->levels->getBlocksBroken($name) + $mods->levels->getTempBlockBreak($name);

                        $playTime = $mods->levels->getTotalPlayTimeToDHMS($name);
                        $bossesKilled = $mods->levels->getBossesKilled($name);

                        $nick = $mods->nicknames->getNick($name);
                        $rankPrice = $mods->ranks->getNextRankPrice($name) * $level;

                        $money = $mods->economy->getMoney($name);

                        $content = "&l&8» &r&7blocks broken &f$broken\n";
                        $content .= "&l&8» &r&7blocks placed &f$placed\n";
                        $content .= "&l&8» &r&7bosses killed &f$bossesKilled\n";
                        $content .= "&l&8» &r&7deaths &f$deaths\n";
                        $content .= "&l&8» &r&7kills &f$kills\n";
                        $content .= "&l&8» &r&7money &f$money\n";
                        $content .= "&l&8» &r&7next rank cost &f$rankPrice\n";
                        $content .= "&l&8» &r&7nick &f" . ($nick !== null ? "~" . $nick : "N/A") . "\n";
                        $content .= "&l&8» &r&7play time &f$playTime\n";
                        $content .= "&l&8» &r&7prestige &f$level\n";
                        $content .= "&l&8» &r&7rank &f$rank";

                        $ui->setContent(RandomUtils::colorMessage($content));
                }else{
                        $ui->setContent(RandomUtils::colorMessage("profile not found."));
                }

                $ui->send($player);
        }

        public function handleResponse(Player $player, $formData){
        }
}