<?php
namespace PrestigeSociety\Data;
use PrestigeSociety\Core\PrestigeSocietyCore;
class ModuleConfigurations{
        /** @var string */
        protected string $path;

        // A
        // B
        public array $borders = [];
        /** @var array */
        public array $bosses = [];

        // C
        /** @var array */
        public array $casino = [];
        /** @var array */
        //public $ce_crates = [];
        /** @var array */
        public array $crates = [];
        /** @var array */
        public array $credit_shop = [];
        /** @var array */
        public array $custom_items = [];

        // D
        /** @var array */
        public array $directions = [];
        /** @var array */
        public array $dialogue = [];

        // E
        // F
        // G
        // H
        /** @var array */
        public array $hud = [];

        // I
        // J
        // K
        /** @var array */
        public array $kits = [];

        // L
        /** @var array */
        public array $land_protector = [];
        /** @var array */
        public array $levels = [];

        // M
        /** @var array */
        public array $management = [];

        // N
        // O
        // P
        // Q
        // R
        /** @var array */
        public array $repair_prices = [];

        // S
        // T
        // U
        // V
        /** @var array */
        //public $vanilla_crates = [];

        // W
        /** @var array */
        public array $warzone = [];
        /** @var array */
        public array $worlds = [];

        // X
        // Y
        // Z

        /**
         * ModuleConfigurations constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->path = $core->getDataFolder();

                // A
                // B
                $this->borders = yaml_parse_file($core->getDataFolder() . "borders.yml");
                $this->bosses = yaml_parse_file($core->getDataFolder() . "bosses.yml");

                // C
                $this->casino = yaml_parse_file($core->getDataFolder() . "casino.yml");
                //$this->ce_crates = yaml_parse_file($core->getDataFolder() . "ce_crates.yml");
                $this->crates = yaml_parse_file($core->getDataFolder() . "crates.yml");
                $this->credit_shop = yaml_parse_file($core->getDataFolder() . "credit_shop.yml");
                $this->custom_items = yaml_parse_file($core->getDataFolder() . "custom_items.yml");

                // D
                $this->directions = yaml_parse_file($core->getDataFolder() . "directions.yml");

                // E
                // F
                // G
                // H
                $this->hud = yaml_parse_file($core->getDataFolder() . "hud.yml");

                // I
                // J
                // K
                $this->kits = yaml_parse_file($core->getDataFolder() . "kits.yml");

                // L
                $this->land_protector = yaml_parse_file($core->getDataFolder() . "land_protector.yml");
                $this->levels = yaml_parse_file($core->getDataFolder() . "levels.yml");

                // M
                $this->management = yaml_parse_file($core->getDataFolder() . "management.yml");

                // N
                // O
                // P
                // Q
                // R
                $this->repair_prices = yaml_parse_file($core->getDataFolder() . "repair_prices.yml");

                // S
                // T
                // U
                // V
                //$this->vanilla_crates = yaml_parse_file($core->getDataFolder() . "vanilla_crates.yml");

                // W
                $this->warzone = yaml_parse_file($core->getDataFolder() . "warzone.yml");
                $this->worlds = yaml_parse_file($core->getDataFolder() . "worlds.yml");

                // X
                // Y
                // Z
        }

        public function saveBordersConfig(){
                yaml_emit_file($this->path . "borders.yml", $this->borders);
        }

        public function saveBossesConfig(){
                yaml_emit_file($this->path . "bosses.yml", $this->bosses);
        }

        public function saveCasinoConfig(){
                yaml_emit_file($this->path . "casino.yml", $this->casino);
        }

        public function saveCratesConfig(){
                yaml_emit_file($this->path . "crates.yml", $this->crates);
        }

        public function saveCeCratesConfig(){
                //yaml_emit_file($this->path . "ce_crates.yml", $this->ce_crates);
        }

        public function saveCustomItemsConfig(){
                yaml_emit_file($this->path . "custom_items.yml", $this->custom_items);
        }

        public function saveDirectionsConfig(){
                yaml_emit_file($this->path . "directions.yml", $this->directions);
        }

        public function saveHUDConfig(){
                yaml_emit_file($this->path . "hud.yml", $this->hud);
        }

        public function saveKitsConfig(){
                yaml_emit_file($this->path . "kits.yml", $this->kits);
        }

        public function saveLandProtectorConfig(){
                yaml_emit_file($this->path . "land_protector.yml", $this->land_protector);
        }

        public function saveLevelsConfig(){
                yaml_emit_file($this->path . "levels.yml", $this->levels);
        }

        public function saveManagementConfig(){
                yaml_emit_file($this->path . "management.yml", $this->management);
        }

        public function saveRepairPricesConfig(){
                yaml_emit_file($this->path . "repair_prices.yml", $this->repair_prices);
        }

        public function saveVanillaCratesConfig(){
                //yaml_emit_file($this->path . "vanilla_crates.yml", $this->vanilla_crates);
        }

        public function saveWarzoneConfig(){
                yaml_emit_file($this->path . "warzone.yml", $this->warzone);
        }

        public function saveWorldsConfig(){
                yaml_emit_file($this->path . "worlds.yml", $this->worlds);
        }
}