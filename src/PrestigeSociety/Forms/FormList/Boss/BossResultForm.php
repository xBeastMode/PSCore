<?php
namespace PrestigeSociety\Forms\FormList\Boss;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class BossResultForm extends FormHandler{
        public function send(Player $player){
                $data = $this->getData();

                $ui = new SimpleForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&8&lBOSS FIGHT RESULTS"));

                $content = "";

                if($data[0] !== false){
                        /** @var Player[]|int[] $mostDamage */
                        $mostDamage = $data[0];

                        $top = $mostDamage[0]->getName();
                        $topDamage = $mostDamage[1];
                        $topPercent = $mostDamage[2];
                        $maxHealth = $mostDamage[3];

                        $content = "&l&8» &f{$top} &7dealt the most damage: &f{$topDamage}&7/&f{$maxHealth} &7(&f{$topPercent} &7percent) hit points!\n\n";
                }

                foreach($data[1] as $damager){
                        /** @var Player[]|int[] $mostDamage */
                        $mostDamage = $damager;
                        $name = $mostDamage[0]->getName();
                        $damage = $damager[1];
                        $percent = $damager[2];
                        $maxHealth = $damager[3];

                        $content .= "&l&8» &f{$name} &7dealt: &f{$damage}&7/&f{$maxHealth} &7(&f{$percent} &7percent) hit points!\n\n";
                }

                $ui->setContent(RandomUtils::colorMessage($content));
                $ui->setButton(RandomUtils::colorMessage("&8awesome!"));
                $ui->send($player);
        }

        public function handleResponse(Player $player, $formData){
        }
}