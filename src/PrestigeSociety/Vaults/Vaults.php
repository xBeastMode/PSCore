<?php
namespace PrestigeSociety\Vaults;
use pocketmine\block\inventory\ChestInventory;
use pocketmine\block\inventory\DoubleChestInventory;
use pocketmine\item\Item;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\DataModels\VaultsModel;
use PrestigeSociety\InventoryMenu\Inventory\MenuInventory;
use PrestigeSociety\InventoryMenu\TransactionData;
class Vaults{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * Vaults constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param     $player
         * @param int $vaultId
         *
         * @return bool
         */
        public function vaultExists($player, int $vaultId): bool{
                $record = VaultsModel::query()->where([["name", "=", RandomUtils::getName($player)], ["vaultId", "=", $vaultId]]);
                return $record->exists();
        }

        /**
         * @param                $player
         * @param string         $contents
         * @param int            $vaultId
         */
        public function saveRawContents($player, string $contents, int $vaultId){
                VaultsModel::query()->updateOrCreate([["name", "=", RandomUtils::getName($player)], ["vaultId", "=", $vaultId]], [
                    "name" => RandomUtils::getName($player),
                    "vaultId" => $vaultId,
                    "contents" => $contents,
                ]);
        }

        /**
         * @param       $player
         * @param array $contents
         * @param int   $vaultId
         */
        public function saveContents($player, array $contents, int $vaultId){
                $contents = array_filter($contents, function ($item){
                        return $item instanceof Item;
                });
                $this->saveRawContents($player, $this->serializeContents($contents), $vaultId);
        }

        /**
         * @param                                     $player
         * @param ChestInventory|DoubleChestInventory $inventory
         * @param int                                 $vaultId
         */
        public function saveChestInventoryContents($player, ChestInventory|DoubleChestInventory $inventory, int $vaultId){
                $this->saveRawContents($player, $this->serializeContents($inventory->getContents()), $vaultId);
        }

        /**
         * @param     $player
         * @param int $vaultId
         *
         * @return array|null
         */
        public function getRawContents($player, int $vaultId): ?string{
                $record = VaultsModel::query()->where([["name", "=", RandomUtils::getName($player)], ["vaultId", "=", $vaultId]]);

                if(!$record->exists()){
                        return null;
                }

                return $record->value("contents");
        }

        /**
         * @param     $player
         * @param int $vaultId
         *
         * @return array
         */
        public function getContents($player, int $vaultId): array{
                $contents = $this->getRawContents($player, $vaultId);
                return $contents !== null ? $this->deserializeContents($contents) : [];
        }

        /**
         * @param string $items
         *
         * @return Item[]
         */
        public function deserializeContents(string $items): array{
                return array_map(function (array $data){
                        return Item::jsonDeserialize($data);
                }, unserialize($items));
        }

        /**
         * @param array $items
         *
         * @return string
         */
        public function serializeContents(array $items): string{
                return serialize(array_map(function (Item $item){
                        return $item->jsonSerialize();
                }, $items));
        }

        /**
         * @param Player $player
         * @param int    $vaultId
         * @param string $username
         *
         * @return bool
         */
        public function openWindow(Player $player, int $vaultId, string $username): bool{
                if($vaultId < 0){
                        return false;
                }

                if(PermissionManager::getInstance()->getPermission("pv.double.$vaultId")){
                        PermissionManager::getInstance()->addPermission(new Permission("pv.double.$vaultId"));
                }

                $function = $player->hasPermission("pv.double") || $player->hasPermission("pv.double.$vaultId") ? "openDoubleChestInventory" : "openInventory";

                $chest_inventory = $this->core->module_loader->inventory_menu->{$function}($player, function (TransactionData $data){
                        return false;
                }, [
                    "title" => RandomUtils::colorMessage("&8PRIVATE VAULT &0#$vaultId" . ($username !== $player->getName() ? " (" . $username . ")" : "")),
                    "height" => 3,
                ]);

                $this->core->module_loader->inventory_menu->setCloseCallback($player, function () use (&$chest_inventory, $username, $vaultId){
                        $this->saveChestInventoryContents($username, $chest_inventory, $vaultId);
                });

                $chest_inventory->setContents($this->getContents($username, $vaultId));

                if($chest_inventory instanceof MenuInventory){
                        $chest_inventory->setContents($this->getContents($username, $vaultId));
                        return true;
                }

                return false;
        }
}