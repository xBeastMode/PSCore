<?php
namespace PrestigeSociety\Worlds;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Worlds\Inventory\WorldInventory;
class Worlds{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;
        /** @var WorldInventory */
        protected WorldInventory $world_inventory;
        /**
         * Worlds constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
                $this->core->getServer()->getPluginManager()->registerEvents(new WorldsListener($core), $core);

                $this->world_inventory = new WorldInventory($core);
        }

        /**
         * @return WorldInventory
         */
        public function getWorldInventory(): WorldInventory{
                return $this->world_inventory;
        }

        /**
         * @return bool
         */
        public function isPerWorldInventoryEnabled(): bool{
                return (bool)$this->core->module_configurations->worlds["per_world_inventory"];
        }

        /**
         * @param string $world
         *
         * @return string
         */
        public function getTargetInventoryType(string $world): string{
                return $this->core->module_configurations->worlds["world_inventory_type"][$world] ?? "@linked";
        }

        /**
         * @param string $world
         *
         * @return int|null
         */
        public function getWorldTime(string $world): ?int{
                return $this->core->module_configurations->worlds["world_time"][$world] ?? null;
        }

        /**
         * @param string $command
         * @param string $world
         *
         * @return bool
         */
        public function isCommandBlocked(string $command, string $world): bool{
                return in_array($command, $this->core->module_configurations->worlds["blocked_commands"][$world] ?? []);
        }
}