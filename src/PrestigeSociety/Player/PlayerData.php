<?php
namespace PrestigeSociety\Player;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormList\Player\PlayerSettingsForm;
use PrestigeSociety\Forms\FormList\Player\ProfileForm;
use PrestigeSociety\Forms\FormManager;
use PrestigeSociety\Player\Data\Settings;
class PlayerData{
        public int $PLAYER_SETTINGS_ID = 0;
        public int $PROFILE_ID = 0;

        /** @var Settings[] */
        protected array $player_settings = [];
        /** @var PlayerManager[] */
        protected array $player_managers = [];

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * PlayerListener constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
                $this->PLAYER_SETTINGS_ID = FormManager::getNextFormId();
                $this->PROFILE_ID = FormManager::getNextFormId();

                $this->core->module_loader->form_manager->registerHandler($this->PLAYER_SETTINGS_ID, PlayerSettingsForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->PROFILE_ID, ProfileForm::class);
        }

        /**
         * @param $player
         *
         * @return Settings
         */
        public function getPlayerSettings($player): Settings{
                return $this->player_settings[RandomUtils::getName($player)] ?? $this->player_settings[RandomUtils::getName($player)] = Settings::fetch($player);
        }

        /**
         * @param $player
         *
         * @return Settings
         */
        public function getFreshPlayerSettings($player): Settings{
                return Settings::fetch($player);
        }

        /**
         * @param Player $player
         *
         * @return PlayerManager
         */
        public function getPlayerManager(Player $player): PlayerManager{
                return $this->player_managers[RandomUtils::getName($player)] ?? $this->player_managers[RandomUtils::getName($player)] = new PlayerManager($this->core, $player);
        }
}