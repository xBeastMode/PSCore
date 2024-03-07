<?php
namespace PrestigeSociety\Shop;
use pocketmine\item\Item;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\DataModels\ShopModel;
use PrestigeSociety\Forms\FormList\Shop\AddShopForm;
use PrestigeSociety\Forms\FormList\Shop\RemoveShopForm;
use PrestigeSociety\Forms\FormList\Shop\SelectAmountForm;
use PrestigeSociety\Forms\FormList\Shop\SelectCategoryForm;
use PrestigeSociety\Forms\FormList\Shop\SelectItemForm;
use PrestigeSociety\Forms\FormList\Shop\ShopForm;
use PrestigeSociety\Forms\FormManager;
class Shop{
        /** @var int */
        public int $SELECT_AMOUNT_ID;
        public int $SELECT_ITEM_ID;
        public int $SHOP_ID;
        public int $SELECT_CATEGORY_ID = 0;
        public int $REMOVE_SHOP_ID = 0;
        public int $ADD_SHOP_ID = 0;

        /** @var string[][] */
        public array $queue = [];

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var array */
        protected array $selecting = [];

        /** @var array */
        protected array $potion_names = [];

        /**
         * Shop constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->ADD_SHOP_ID = FormManager::getNextFormId();
                $this->REMOVE_SHOP_ID = FormManager::getNextFormId();
                $this->SELECT_CATEGORY_ID = FormManager::getNextFormId();
                $this->SHOP_ID = FormManager::getNextFormId();
                $this->SELECT_ITEM_ID = FormManager::getNextFormId();
                $this->SELECT_AMOUNT_ID = FormManager::getNextFormId();

                $this->core = $core;

                $formManager = $this->core->module_loader->form_manager;
                $formManager->registerHandler($this->ADD_SHOP_ID, AddShopForm::class);
                $formManager->registerHandler($this->REMOVE_SHOP_ID, RemoveShopForm::class);
                $formManager->registerHandler($this->SELECT_CATEGORY_ID, SelectCategoryForm::class);
                $formManager->registerHandler($this->SELECT_AMOUNT_ID, SelectAmountForm::class);
                $formManager->registerHandler($this->SELECT_ITEM_ID, SelectItemForm::class);
                $formManager->registerHandler($this->SHOP_ID, ShopForm::class);
        }

        /**
         * @param int $category
         *
         * @return string
         */
        public function categoryToString(int $category): string{
                $ids = [
                    "Armor", // 0
                    "Beds",
                    "Carpet",
                    "Common Blocks",
                    "Concrete",
                    "Decoration", // 5
                    "Doors/Gates",
                    "Farming",
                    "Fencing",
                    "Food",
                    "Gadgets", // 10
                    "Glass",
                    "Lighting",
                    "Minerals",
                    "Miscellaneous",
                    "Potions", // 15
                    "Slabs",
                    "Terracotta",
                    "Tools",
                    "Weapons",
                    "Wood",
                    "Wool",
                ];
                return $ids[$category] ?? "Unknown";
        }

        /**
         * @param int $meta
         *
         * @return string
         */
        public function potionMetaToName(int $meta): string{
                return RandomUtils::potionMetaToName($meta);
        }

        /**
         * @API
         *
         * @param int $category
         * @param int $id
         *
         * @return bool
         */
        public function shopExists(int $category, int $id): bool{
                return ShopModel::query()->where([
                    ["id", "=", $id],
                    ["category", "=", $category]
                ])->exists();
        }

        /**
         * @API
         *
         * @param Item $item
         * @param int  $price
         * @param int  $category
         *
         * @return bool
         */
        public function addNewShop(Item $item, int $price, int $category): bool{
                if($category < 0 || $category > 21){
                        return false;
                }

                $itemName = $item->getName();
                $itemId = $item->getId();
                $itemMeta = $item->getMeta();
                $amount = $item->getCount();

                ShopModel::query()->create([
                    "item" => $itemName,
                    "price" => $price,
                    "amount" => $amount,
                    "itemId" => $itemId,
                    "itemMeta" => $itemMeta,
                    "category" => $category
                ]);

                return true;
        }

        /**
         * @API
         *
         * @param int $category
         * @param int $id
         */
        public function removeShop(int $category, int $id){
                $record = ShopModel::query()->where([
                    ["id", "=", $id],
                    ["category", "=", $category]
                ]);

                if($record->exists()){
                        $record->delete();
                }
        }

        /**
         * @return array
         */
        public function getAllShops(): array{
                return ShopModel::all()->toArray();
        }


        /**
         * @param int $category
         * @param int $id
         *
         * @return array
         */
        public function getShop(int $category, int $id): array{
                $record = ShopModel::query()->where([
                    ["id", "=", $id],
                    ["category", "=", $category]
                ]);
                if($record->exists()){
                        return $record->get()->first()->toArray();
                }
                return [];
        }

        /**
         * @param int $category
         *
         * @return array
         */
        public function getShopItems(int $category): array{
                $record = ShopModel::query()->where("category", "=", $category);
                if($record->exists()){
                        return $record->get()->all();
                }
                return [];
        }
}