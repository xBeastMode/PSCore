<?php
namespace PrestigeSociety\Teleport;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Teleport\Handle\TeleportQueue;
use PrestigeSociety\Teleport\Task\TeleportDelayTask;
class Teleport{
        const DEFAULT_OWNER = "SERVER";

        const INSTANT_TELEPORT_PERMISSION = "teleport.instant";
        const INSTANT_HOME_TELEPORT_PERMISSION = "home.instant";
        const INSTANT_SPAWN_TELEPORT_PERMISSION = "spawn.instant";
        const INSTANT_TPA_TELEPORT_PERMISSION = "tpa.instant";
        const INSTANT_WARP_TELEPORT_PERMISSION = "warp.instant";
        const INSTANT_BACK_TELEPORT_PERMISSION = "back.instant";

        const INFINITE_HOMES_PERMISSION = "home.infinite";
        const DEFAULT_MAX_HOMES = 2;

        /** @var Config */
        public Config $messages;
        /** @var TeleportAPI */
        public TeleportAPI $teleport_api;
        /** @var HomeAPI */
        public HomeAPI $home_api;
        /** @var WarpAPI */
        public WarpAPI $warp_api;
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * Teleport constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                $this->core->saveResource("teleport_messages.yml");
                $this->messages = new Config($this->core->getDataFolder() . "teleport_messages.yml", Config::YAML);
                $this->teleport_api = new TeleportAPI($core);
                $this->home_api = new HomeAPI($core);
                $this->warp_api = new WarpAPI($core);

                $this->core->getServer()->getPluginManager()->registerEvents(new EventListener($core), $this->core);
                DefaultPermissions::registerPermission(new Permission("warp.all", ""), [RandomUtils::getOperatorPermission()]);

                foreach($this->warp_api->getWarps() as $warp){
                        DefaultPermissions::registerPermission(new Permission("warp." . $warp["name"], ""), [RandomUtils::getOperatorPermission()]);
                }
        }

        /**
         * @param Position $position
         * @param string   $owner
         */
        public function setSpawn(Position $position, string $owner = self::DEFAULT_OWNER){
                $this->warp_api->setWarp("spawn", $position->x, $position->y, $position->z, $position->getWorld()->getDisplayName(), $owner);
        }

        /**
         * @return null|Position
         */
        public function getSpawn(): ?Position{
                return $this->warp_api->getWarpPosition("spawn");
        }

        /**
         * @return Config
         */
        public function getMessages(){
                return $this->messages;
        }

        /**
         * @param string $message
         *
         * @return null|string
         */
        public function getMessage(string $message): ?string{
                return $this->messages->get($message, $message);
        }

        /**
         * @return HomeAPI
         */
        public function getHomeAPI(): HomeAPI{
                return $this->home_api;
        }

        /**
         * @return WarpAPI
         */
        public function getWarpAPI(): WarpAPI{
                return $this->warp_api;
        }

        /**
         * @return TeleportAPI
         */
        public function getTeleportAPI(): TeleportAPI{
                return $this->teleport_api;
        }

        /**
         * @return int
         */
        public function getMaxHomes(): int{
                return $this->core->getConfig()->getNested("teleport.max_homes", self::DEFAULT_MAX_HOMES);
        }

        /**
         * @param Player   $player
         * @param Position $position
         * @param int      $delay
         * @param bool     $showMessage
         */
        public function teleport(Player $player, Position $position, int $delay = 0, bool $showMessage = true){
                $this->core->getScheduler()->scheduleRepeatingTask(new TeleportDelayTask($this->core, $player, $position, $delay, $showMessage), 20);
                TeleportQueue::addToQueue($player);
        }

        /**
         * @param Player $player
         * @param array  $options
         *
         * @return int
         */
        public function getTeleportDelay(Player $player, array $options = []): int{
                $delay = $this->core->getConfig()->getNested("teleport." . ($options["module"] ?? "spawn") . "_delay", null) ?? 0;
                $permission = $player->hasPermission(self::INSTANT_TELEPORT_PERMISSION) || ($player->hasPermission($options["permission"] ?? self::INSTANT_SPAWN_TELEPORT_PERMISSION));
                return $permission ? 0 : (($options["ticks"] ?? false) ? intval($delay) * 20 : intval($delay));
        }

        /**
         * @param Player $player
         * @param array  $options
         *
         * @return string
         */
        public function getTeleportMessage(Player $player, array $options = []): string{
                $permission = $player->hasPermission(self::INSTANT_TELEPORT_PERMISSION) || ($player->hasPermission($options["permission"] ?? self::INSTANT_SPAWN_TELEPORT_PERMISSION));
                $message = $permission ? $this->getMessage("teleport_" . ($options["module"] ?? "spawn")) : $this->getMessage("teleport_" . ($options["module"] ?? "spawn") . "_delayed");

                $keys = $options["vars"] ?? [];
                $values = $options["vars"] ?? [];

                return str_replace(array_keys($keys), array_values($values), $message);
        }
}