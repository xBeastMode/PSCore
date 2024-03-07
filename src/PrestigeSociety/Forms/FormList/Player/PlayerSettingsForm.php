<?php
namespace PrestigeSociety\Forms\FormList\Player;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Player\Data\Settings;
use PrestigeSociety\UIForms\CustomForm;
class PlayerSettingsForm extends FormHandler{
        /**
         * @param Player $player
         *
         * @return void
         */
        public function send(Player $player){
                $ui = new CustomForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&8&lPLAYER SETTINGS"));
                $settings = $this->core->module_loader->player_data->getPlayerSettings($player);
                $ui->setToggle(RandomUtils::colorMessage("&7enable hud"), $settings->get(Settings::SETTING_ENABLE_HUD));
                $ui->setToggle(RandomUtils::colorMessage("&7tp to spawn on join"), $settings->get(Settings::SETTING_TELEPORT_ON_JOIN));
                $ui->setInput(RandomUtils::colorMessage("&7your status"), "", $settings->get(Settings::SETTING_PLAYER_STATUS));
                $ui->setToggle(RandomUtils::colorMessage("&7suppress crate messages"), $settings->get(Settings::SETTING_SUPPRESS_CRATE_MESSAGES, true));
                $ui->setToggle(RandomUtils::colorMessage("&7suppress casino messages"), $settings->get(Settings::SETTING_SUPPRESS_CASINO_MESSAGES, true));
                $ui->setToggle(RandomUtils::colorMessage("&7affected by stack stick"), $settings->get(Settings::SETTING_AFFECTED_BY_STACK_STICK, true));
                $ui->setToggle(RandomUtils::colorMessage("&7affected by ride stick"), $settings->get(Settings::SETTING_AFFECTED_BY_RIDE_STICK, true));
                $ui->setDropdown(RandomUtils::colorMessage("&7join sound"), [
                    "None",
                    "COD: Black Ops II Tranzit Theme",
                    "Roller Coaster",
                    "Stranger Things Intro (C418 Remix)",
                    "Reese Bass (Boss Spawn Sound)"
                ], $settings->get(Settings::JOIN_SOUND, 1));
                $ui->send($player);
        }

        /**
         * @param Player $player
         * @param        $formData
         *
         * @return void
         */
        public function handleResponse(Player $player, $formData){
                $settings = $this->core->module_loader->player_data->getPlayerSettings($player);

                if($formData[0] !== $settings->get(Settings::SETTING_ENABLE_HUD)){
                        $this->core->module_loader->hud->toggleHUD($player);
                }

                $settings->set(Settings::SETTING_ENABLE_HUD, $formData[0]);
                $settings->set(Settings::SETTING_TELEPORT_ON_JOIN, $formData[1]);
                $settings->set(Settings::SETTING_PLAYER_STATUS, $formData[2]);
                $settings->set(Settings::SETTING_SUPPRESS_CRATE_MESSAGES, $formData[3]);
                $settings->set(Settings::SETTING_SUPPRESS_CASINO_MESSAGES, $formData[4]);
                $settings->set(Settings::SETTING_AFFECTED_BY_STACK_STICK, $formData[5]);
                $settings->set(Settings::SETTING_AFFECTED_BY_RIDE_STICK, $formData[6]);
                $settings->set(Settings::JOIN_SOUND, $formData[7]);
                $settings->save();

                $player->sendMessage(RandomUtils::colorMessage($this->core->getMessage("settings", "settings_changed")));
        }
}