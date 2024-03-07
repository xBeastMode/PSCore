<?php
namespace PrestigeSociety\Data;
use PrestigeSociety\Async\AsyncManager;
use PrestigeSociety\Auth\Auth;
use PrestigeSociety\Bosses\Bosses;
use PrestigeSociety\Casino\Casino;
use PrestigeSociety\Chat\Chat;
use PrestigeSociety\CombatLogger\CombatLogger;
use PrestigeSociety\Core\EntityLinker;
use PrestigeSociety\Core\FunBox;
use PrestigeSociety\Core\HUD;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Crates\Crates;
use PrestigeSociety\CreditShop\CreditShop;
use PrestigeSociety\CustomItems\CustomItems;
use PrestigeSociety\DataProvider\DataProvider;
use PrestigeSociety\Credits\Credits;
use PrestigeSociety\Directions\Directions;
use PrestigeSociety\Economy\Economy;
use PrestigeSociety\Enchants\Enchants;
use PrestigeSociety\Events\Events;
use PrestigeSociety\Forms\FormManager;
use PrestigeSociety\Hats\Hats;
use PrestigeSociety\InventoryMenu\InventoryMenu;
use PrestigeSociety\Kits\Kits;
use PrestigeSociety\LandProtector\LandProtector;
use PrestigeSociety\Levels\Levels;
use PrestigeSociety\Management\Management;
use PrestigeSociety\MineResetter\MineResetter;
use PrestigeSociety\Nicknames\Nicknames;
use PrestigeSociety\Player\PlayerData;
use PrestigeSociety\Portals\Portals;
use PrestigeSociety\PowerUps\PowerUps;
use PrestigeSociety\ProtectionStones\ProtectionStones;
use PrestigeSociety\Ranks\Ranks;
use PrestigeSociety\Recovery\Recovery;
use PrestigeSociety\Restarter\Restarter;
use PrestigeSociety\Shop\Shop;
use PrestigeSociety\Spawners\Spawners;
use PrestigeSociety\Statistics\Statistics;
use PrestigeSociety\Teleport\Teleport;
use PrestigeSociety\Vaults\Vaults;
use PrestigeSociety\Warzone\Warzone;
use PrestigeSociety\Worlds\Worlds;
class ModuleLoader{
        /** @var PrestigeSocietyCore */
        protected $core;

        /** @var Auth */
        //public Auth $auth;
        /** @var Chat */
        public Chat $chat;
        /** @var Restarter */
        public Restarter $restarter;
        /** @var LandProtector */
        public LandProtector $land_protector;
        /** @var Vaults */
        public Vaults $vaults;
        /** @var CombatLogger */
        public CombatLogger $combat_logger;
        /** @var Levels */
        public Levels $levels;
        /** @var Economy */
        public Economy $economy;
        /** @var Credits */
        public Credits $credits;
        /** @var Teleport */
        public Teleport $teleport;
        /** @var Shop */
        public Shop $shop;
        /** @var FunBox */
        public FunBox $fun_box;
        /** @var Ranks */
        public Ranks $ranks;
        /** @var MineResetter */
        public MineResetter $mine_resetter;
        /** @var Kits */
        public Kits $kits;
        /** @var Nicknames */
        public Nicknames $nicknames;
        /** @var Enchants */
        public Enchants $enchants;
        /** @var HUD */
        public HUD $hud;
        /** @var DataProvider */
        public DataProvider $data_provider;
        /** @var FormManager */
        public FormManager $form_manager;
        /** @var AsyncManager */
        public AsyncManager $async_manager;
        /** @var PlayerData */
        public PlayerData $player_data;
        /** @var Worlds */
        public Worlds $worlds;
        /** @var ProtectionStones */
        public ProtectionStones $protection_stones;
        /** @var Statistics */
        public Statistics $statistics;
        /** @var Portals */
        public Portals $portals;
        /** @var Bosses */
        public Bosses $bosses;
        /** @var Spawners */
        public Spawners $spawners;
        /** @var CreditShop */
        public CreditShop $credit_shop;
        /** @var Directions */
        public Directions $directions;
        /** @var Hats */
        public Hats $hats;
        /** @var EntityLinker */
        public EntityLinker $entity_linker;
        /** @var PowerUps */
        public PowerUps $power_ups;
        /** @var Casino */
        public Casino $casino;
        /** @var Crates */
        public Crates $crates;
        /** @var CustomItems */
        public CustomItems $custom_items;
        /** @var InventoryMenu */
        public InventoryMenu $inventory_menu;
        /** @var Events */
        public Events $events;
        /** @var Management */
        public Management $management;
        /** @var Recovery */
        public Recovery $recovery;
        /** @var Warzone */
        public Warzone $warzone;

        /**
         * ModuleLoader constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        public function loadModules(){
                $this->async_manager = new AsyncManager($this->core);
                $this->data_provider = new DataProvider($this->core);

                $this->form_manager = new FormManager($this->core);
                $this->player_data = new PlayerData($this->core);

                $this->worlds = new Worlds($this->core);

                //$this->auth = new Auth($this->core);
                $this->chat = new Chat($this->core);
                $this->land_protector = new LandProtector($this->core);

                $this->vaults = new Vaults($this->core);
                $this->combat_logger = new CombatLogger($this->core);

                $this->levels = new Levels($this->core);
                $this->economy = new Economy($this->core);

                $this->credits = new Credits($this->core);
                $this->teleport = new Teleport($this->core);

                $this->shop = new Shop($this->core);
                $this->ranks = new Ranks($this->core);

                $this->kits = new Kits($this->core);
                $this->nicknames = new Nicknames($this->core);

                $this->enchants = new Enchants($this->core);
                $this->fun_box = new FunBox($this->core);

                $this->mine_resetter = new MineResetter($this->core);
                $this->hud = new HUD($this->core);

                $this->protection_stones = new ProtectionStones($this->core);
                $this->statistics = new Statistics($this->core);

                $this->portals = new Portals($this->core);
                $this->bosses = new Bosses($this->core);

                //$this->spawners = new Spawners($this->core);

                $this->credit_shop = new CreditShop($this->core);
                $this->directions = new Directions($this->core);

                $this->hats = new Hats($this->core);
                $this->entity_linker = new EntityLinker($this->core);

                $this->power_ups = new PowerUps($this->core);
                $this->casino = new Casino($this->core);

                $this->crates = new Crates($this->core);
                $this->custom_items = new CustomItems($this->core);

                $this->inventory_menu = new InventoryMenu($this->core);
                $this->restarter = new Restarter((int) $this->core->getConfig()->getNested("restarter.time"));

                $this->events = new Events($this->core);
                $this->management = new Management($this->core);

                $this->recovery = new Recovery($this->core);
                $this->warzone = new Warzone($this->core);

                $this->land_protector->initFolder();
        }
}