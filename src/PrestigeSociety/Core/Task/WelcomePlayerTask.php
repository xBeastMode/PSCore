<?php
namespace PrestigeSociety\Core\Task;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Player\Data\Settings;

class WelcomePlayerTask extends Task{
        private PrestigeSocietyCore $core;

        private string $title;
        private string $subtitle;
        /** @var Player|null */
        private ?Player $player = null;

        /**
         * WelcomePlayerTask constructor.
         *
         * @param PrestigeSocietyCore $owner
         * @param                     $title
         * @param                     $subtitle
         * @param Player              $player
         */
        public function __construct(PrestigeSocietyCore $owner, $title, $subtitle, Player $player){
                $this->core = $owner;
                $this->title = $title;
                $this->subtitle = $subtitle;
                $this->player = $player;
        }

        /**
         * Actions to execute when run
         *
         * @return void
         */
        public function onRun(): void{
                $this->player->sendTitle(RandomUtils::colorMessage($this->title), RandomUtils::colorMessage($this->subtitle), 20, 120, 20);

                $sounds = [
                    "bass.welcome",
                    "bass.welcome2",
                    "bass.welcome3",
                    "bass.reese"
                ];

                $settings = $this->core->module_loader->player_data->getPlayerSettings($this->player);
                $join_sound = $settings->get(Settings::JOIN_SOUND, 1);

                if($join_sound !== 0){
                        RandomUtils::playSound($sounds[$join_sound - 1], $this->player, 1000, 1, true);
                }
        }
}