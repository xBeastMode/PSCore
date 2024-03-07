<?php
namespace PrestigeSociety\Enchants;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use PrestigeSociety\Core\PrestigeSocietyCore as PrestigeSocietyCoreAlias;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormList\Enchants\ApplyEnchantsForm;
use PrestigeSociety\Forms\FormList\Enchants\BuyEnchantConfirmForm;
use PrestigeSociety\Forms\FormList\Enchants\ChooseEnchantForm;
use PrestigeSociety\Forms\FormList\Enchants\ConfirmApplyForm;
use PrestigeSociety\Forms\FormList\Enchants\ConfirmMergeForm;
use PrestigeSociety\Forms\FormList\Enchants\ConfirmReplicateForm;
use PrestigeSociety\Forms\FormList\Enchants\EnchantmentsForm;
use PrestigeSociety\Forms\FormList\Enchants\MergeEnchantsForm;
use PrestigeSociety\Forms\FormList\Enchants\RemoveEnchantForm;
use PrestigeSociety\Forms\FormList\Enchants\ReplicateEnchantForm;
use PrestigeSociety\Forms\FormManager;
class Enchants{
        public const ENCHANTMENT_NAMES = [
            "Aqua Affinity",
            "Bane of Arthropods",
            "Blast Protection",
            "Depth Strider",
            "Efficiency",
            "Feather Falling",
            "Fire Aspect",
            "Fire Protection",
            "Flame",
            "Fortune",
            "Frost Walker",
            "Impaling",
            "Infinity",
            "Knockback",
            "Loyalty",
            "Looting",
            "Luck of the Sea",
            "Lure",
            "Mending",
            "Multishot",
            "Piercing",
            "Power",
            "Projectile Protection",
            "Protection",
            "Punch",
            "Quick Charge",
            "Respiration",
            "Riptide",
            "Sharpness",
            "Silk Touch",
            "Smite",
            "Thorns",
            "Unbreaking"
        ];

        public const VANILLA_CONVERTER = [
            "enchantment.weapon.arthropods" => "Bane of Arthropods",
            "enchantment.protect.explosion" => "Blast Protection",
            "enchantment.digging" => "Efficiency",
            "enchantment.protect.fall" => "Feather Falling",
            "enchantment.fire" => "Fire Aspect",
            "enchantment.protect.fire" => "Fire Protection",
            "enchantment.arrowFire" => "Flame",
            "enchantment.mining.fortune" => "Fortune",
            "enchantment.waterwalk" => "Frost Walker",
            "enchantment.arrowInfinite" => "Infinity",
            "enchantment.knockback" => "Knockback",
            "enchantment.weapon.looting" => "Looting",
            "enchantment.fishing.fortune" => "Luck of the Sea",
            "enchantment.fishing.lure" => "Lure",
            "enchantment.mending" => "Mending",
            "enchantment.arrowDamage" => "Power",
            "enchantment.protect.projectile" => "Projectile Protection",
            "enchantment.protect.all" => "Protection",
            "enchantment.arrowKnockback" => "Punch",
            "enchantment.oxygen" => "Respiration",
            "enchantment.damage.all" => "Sharpness",
            "enchantment.untouching" => "Silk Touch",
            "enchantment.weapon.smite" => "Smite",
            "enchantment.thorns" => "Thorns",
            "enchantment.durability" => "Unbreaking"
        ];

        public const ENCHANTMENTS_ID_MAP = [
            "protection" => EnchantmentIds::PROTECTION,
            "fire protection" => EnchantmentIds::FIRE_PROTECTION,
            "feather falling" => EnchantmentIds::FEATHER_FALLING,
            "blast protection" => EnchantmentIds::BLAST_PROTECTION,
            "projectile protection" => EnchantmentIds::PROJECTILE_PROTECTION,
            "thorns" => EnchantmentIds::THORNS,
            "respiration" => EnchantmentIds::RESPIRATION,
            "depth strider" => EnchantmentIds::DEPTH_STRIDER,
            "aqua affinity" => EnchantmentIds::AQUA_AFFINITY,
            "sharpness" => EnchantmentIds::SHARPNESS,
            "smite" => EnchantmentIds::SMITE,
            "bane of arthropods" => EnchantmentIds::BANE_OF_ARTHROPODS,
            "knockback" => EnchantmentIds::KNOCKBACK,
            "fire aspect" => EnchantmentIds::FIRE_ASPECT,
            "looting" => EnchantmentIds::LOOTING,
            "efficiency" => EnchantmentIds::EFFICIENCY,
            "silk touch" => EnchantmentIds::SILK_TOUCH,
            "unbreaking" => EnchantmentIds::UNBREAKING,
            "fortune" => EnchantmentIds::FORTUNE,
            "power" => EnchantmentIds::POWER,
            "punch" => EnchantmentIds::PUNCH,
            "flame" => EnchantmentIds::FLAME,
            "infinity" => EnchantmentIds::INFINITY,
            "luck of the sea" => EnchantmentIds::LUCK_OF_THE_SEA,
            "lure" => EnchantmentIds::LURE,
            "frost walker" => EnchantmentIds::FROST_WALKER,
            "mending" => EnchantmentIds::MENDING,
            "binding" => EnchantmentIds::BINDING,
            "vanishing" => EnchantmentIds::VANISHING,
            "impaling" => EnchantmentIds::IMPALING,
            "riptide" => EnchantmentIds::RIPTIDE,
            "loyalty" => EnchantmentIds::LOYALTY,
            "channeling" => EnchantmentIds::CHANNELING,
            "multishot" => EnchantmentIds::MULTISHOT,
            "piercing" => EnchantmentIds::PIERCING,
            "qucik charge" => EnchantmentIds::QUICK_CHARGE,
            "soul speed" => EnchantmentIds::SOUL_SPEED,
        ];

        public const CUSTOM_ENCHANTMENTS_ID_MAP = [
            "autorepair" => 108,
            "soulbound" => 118,
            "aerial" => 114,
            "backstab" => 122,
            "blessed" => 120,
            "blind" => 101,
            "charge" => 113,
            "cripple" => 109,
            "deathbringer" => 102,
            "deep wounds" => 112,
            "disarming" => 117,
            "disarmor" => 121,
            "gooey" => 103,
            "hallucination" => 119,
            "lifesteal" => 100,
            "lightning" => 123,
            "lucky charm" => 124,
            "poison" => 104,
            "vampire" => 111,
            "wither" => 115,
            "auto aim" => 306,
            "blaze" => 311,
            "bombardment" => 300,
            "bounty hunter" => 309,
            "grappling" => 313,
            "headhunter" => 312,
            "healing" => 310,
            "homing" => 316,
            "missile" => 315,
            "molotov" => 304,
            "paralyze" => 303,
            "piercing" => 307,
            "porkified" => 314,
            "shuffle" => 308,
            "volley" => 305,
            "wither skull" => 301,
            "driller" => 206,
            "energizing" => 202,
            "explosive" => 200,
            "haste" => 207,
            "jackpot" => 212,
            "oxygenate" => 211,
            "quickening" => 203,
            "smelting" => 201,
            "telepathy" => 205,
            "lumberjack" => 204,
            "farmer" => 209,
            "fertilizer" => 208,
            "harvest" => 210,
            "molten" => 400,
            "enlighted" => 401,
            "hardened" => 402,
            "poisoned" => 403,
            "frozen" => 404,
            "obsidian shield" => 405,
            "revulsion" => 406,
            "self destruct" => 407,
            "cursed" => 408,
            "endershift" => 409,
            "drunk" => 410,
            "berserker" => 411,
            "cloaking" => 412,
            "revive" => 413,
            "shrink" => 414,
            "grow" => 415,
            "cactus" => 416,
            "anti knockback" => 417,
            "forcefield" => 418,
            "overload" => 419,
            "armored" => 420,
            "tank" => 421,
            "heavy" => 422,
            "shielded" => 423,
            "poisonous cloud" => 424,
            "antitoxin" => 604,
            "focused" => 603,
            "glowing" => 601,
            "implants" => 600,
            "meditation" => 602,
            "chicken" => 801,
            "enraged" => 804,
            "parachute" => 800,
            "prowl" => 802,
            "spider" => 803,
            "vacuum" => 805,
            "gears" => 500,
            "jetpack" => 503,
            "magma walker" => 504,
            "springs" => 501,
            "stomp" => 502,
            "radar" => 700
        ];

        public int $CONFIRM_REPLICATE_ID;
        public int $REPLICATE_ENCHANT_ID;
        public int $CONFIRM_APPLY_ID;
        public int $CONFIRM_MERGE_ID = 0;
        public int $APPLY_ENCHANTS_ID;
        public int $MERGE_ENCHANTS_ID;
        public int $ENCHANTS_ID;
        public int $BUY_ENCHANT_CONFIRM_ID = 0;
        public int $REMOVE_ENCHANT_ID = 0;
        public int $CHOOSE_ENCHANT_ID = 0;

        /** @var PrestigeSocietyCoreAlias */
        protected PrestigeSocietyCoreAlias $core;

        /** @var int[] */
        public array $enchantments_cost;

        /**
         * Enchants constructor.
         *
         * @param PrestigeSocietyCoreAlias $core
         */
        public function __construct(PrestigeSocietyCoreAlias $core){
                $this->core = $core;

                $defaults = [];

                foreach(self::ENCHANTMENT_NAMES as $enchantment){
                        $enchantment = strtolower(str_replace(" ", "_", $enchantment));
                        $defaults[$enchantment] = 5000;
                }

                $this->enchantments_cost = (new Config($core->getDataFolder() . "enchants_cost.yml", Config::YAML, $defaults))->getAll();

                $this->CHOOSE_ENCHANT_ID = FormManager::getNextFormId();
                $this->BUY_ENCHANT_CONFIRM_ID = FormManager::getNextFormId();
                $this->REMOVE_ENCHANT_ID = FormManager::getNextFormId();
                $this->ENCHANTS_ID = FormManager::getNextFormId();
                $this->MERGE_ENCHANTS_ID = FormManager::getNextFormId();
                $this->CONFIRM_MERGE_ID = FormManager::getNextFormId();
                $this->APPLY_ENCHANTS_ID = FormManager::getNextFormId();
                $this->CONFIRM_APPLY_ID = FormManager::getNextFormId();
                $this->REPLICATE_ENCHANT_ID = FormManager::getNextFormId();
                $this->CONFIRM_REPLICATE_ID = FormManager::getNextFormId();

                $this->core->module_loader->form_manager->registerHandler($this->CHOOSE_ENCHANT_ID, ChooseEnchantForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->BUY_ENCHANT_CONFIRM_ID, BuyEnchantConfirmForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->REMOVE_ENCHANT_ID, RemoveEnchantForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->ENCHANTS_ID, EnchantmentsForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->MERGE_ENCHANTS_ID, MergeEnchantsForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->CONFIRM_MERGE_ID, ConfirmMergeForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->APPLY_ENCHANTS_ID, ApplyEnchantsForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->CONFIRM_APPLY_ID, ConfirmApplyForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->REPLICATE_ENCHANT_ID, ReplicateEnchantForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->CONFIRM_REPLICATE_ID, ConfirmReplicateForm::class);

                //$this->core->getServer()->getPluginManager()->registerEvents(new EventListener($core), $core);
        }

        /**
         * @param string $type
         * @param int    $index
         * @param int    $amount
         *
         * @return null|Item
         */
        public function getShard(string $type, int $index = 0, int $amount = 1): ?Item{
                $index = $index < 0 ? 0 : $index;
                $amount = $amount < 1 ? 1 : $amount;

                $type = $this->enchantments_cost[$type][$index] ?? null;
                if($type === null) return null;

                $item = RandomUtils::parseItemsWithEnchantments([$type["item"]])[0];
                $item->setCount($amount);

                if(RandomUtils::toBool($type["glow"])){
                        $item->getNamedTag()->setTag("ench", new ListTag());
                }

                return $item;
        }

        /**
         * @param Player $player
         * @param int    $index
         * @param int    $amount
         */
        public function getShardRandom(Player $player, int $index = -1, int $amount = 1){
                if($index === -1){
                        $types = ["replicate", "apply", "remove"];
                        foreach($types as $type){
                                foreach($this->enchantments_cost[$type] as $index => $item){
                                        if(RandomUtils::randomFloat(0, 100) <= (float)$item["chance"]){
                                                $player->getInventory()->addItem($this->getShard($type, $index, $amount));
                                        }
                                }
                        }
                }else{
                        $player->getInventory()->addItem($this->getShard($player, $index, $amount));
                }
        }
}