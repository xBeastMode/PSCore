<?php
namespace PrestigeSociety\Player\Data;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\DataModels\SettingsModel;
class Settings{
        public const SETTING_ENABLE_HUD = "enable_hud";
        public const SETTING_TELEPORT_ON_JOIN = "tp_on_join";
        public const SETTING_PLAYER_STATUS = "player_status";
        public const SETTING_ALREADY_PURCHASED = "already_purchased";
        public const SETTING_SUPPRESS_CRATE_MESSAGES = "suppress_crate_messages";
        public const SETTING_SUPPRESS_CASINO_MESSAGES = "suppress_casino_messages";
        public const SETTING_AFFECTED_BY_STACK_STICK = "affected_by_stack_stick";
        public const SETTING_AFFECTED_BY_RIDE_STICK = "affected_by_ride_stick";
        public const SETTING_MAX_HOMES = "max_homes";
        public const CASINO_WINS = "casino_wins";
        public const CASINO_NEXT_PLAYTIME = "casino_next_playtime";
        public const JOIN_SOUND = "join_sound";

        /** @var string|Player */
        protected Player|string $player;
        /** @var array */
        protected array $settings = [];

        /**
         * @param $player
         * @param array  $defaultSettings
         *
         * @return Settings
         */
        public static function fetch($player, array $defaultSettings = []): Settings{
                $record = SettingsModel::query()->where("name", "=", RandomUtils::getName($player));
                if(!$record->exists()){
                        $record->create(["name" => RandomUtils::getName($player), "settings" => json_encode($defaultSettings)]);
                        return new Settings($player, $defaultSettings);
                }
                return new Settings($player, json_decode($record->value("settings"), true));
        }

        /**
         * Player constructor.
         *
         * @param $player
         * @param array  $settings
         */
        public function __construct($player, array $settings){
                $this->player = $player;
                $this->settings = $settings;
        }

        /**
         * @param string $name
         *
         * @return bool
         */
        public function exists(string $name): bool{
                return isset($this->settings[$name]);
        }

        /**
         * @param string $name
         */
        public function delete(string $name){
                unset($this->settings[$name]);
        }

        /**
         * @param string $name
         * @param        $value
         *
         * @return Settings
         */
        public function set(string $name, $value): Settings{
                $this->settings[$name] = $value;
                return $this;
        }

        /**
         * @param string $name
         * @param null   $default
         *
         * @return mixed|null
         */
        public function get(string $name, $default = null){
                return $this->settings[$name] ?? $default;
        }

        public function save(){
                $mod = SettingsModel::query()->find(RandomUtils::getName($this->player));
                $mod->settings = json_encode($this->settings);
                $mod->save();
        }
}