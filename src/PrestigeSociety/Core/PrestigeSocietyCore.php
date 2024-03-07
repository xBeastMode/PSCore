<?php
namespace PrestigeSociety\Core;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\Translatable;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use _64FF00\PurePerms\PurePerms;
use pocketmine\plugin\PluginBase;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Data\CommandLoader;
use PrestigeSociety\Data\ModuleConfigurations;
use PrestigeSociety\Data\ModuleLoader;
use PrestigeSociety\Data\TaskLoader;
use Ramsey\Uuid\Uuid;

class PrestigeSocietyCore extends PluginBase{
        private static ?PrestigeSocietyCore $instance = null;

        /** @var Player[] */
        protected array $in_lobby = [];

        /** @var Config */
        private Config $messages;

        /** @var ModuleLoader */
        public ModuleLoader $module_loader;
        /** @var ModuleConfigurations */
        public ModuleConfigurations $module_configurations;

        public function onLoad(): void{
                while(!self::$instance instanceof $this){
                        self::$instance = $this;
                }
        }

        public function onEnable(): void{
                RandomUtils::init();

                $this->messages = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
                $this->saveDefaultConfig();

                if(!file_exists($this->databasesFolder())){
                        mkdir($this->databasesFolder());
                }

                $this->module_configurations = new ModuleConfigurations($this);
                $this->module_loader = new ModuleLoader($this);
                $this->module_loader->loadModules();

                (new CommandLoader($this))->loadCommands();
                (new TaskLoader($this))->loadTasks();

                $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

        }

        public function onDisable(): void{
                $this->module_loader->hats->despawnAll();
                $this->module_loader->bosses->despawnAll();
                $this->module_loader->warzone->despawnForClose();
                $this->module_loader->levels->saveTempBlockSessionsData();
        }

        #[Pure] public function databasesFolder(): string{
                return $this->getDataFolder() . "database/";
        }

        public function reloadGroupsConfig(){
                $pp = $this->getServer()->getPluginManager()->getPlugin("PurePerms");

                if($pp instanceof PurePerms){
                        $groupsConfig = yaml_parse_file($this->getDataFolder() . "chat_format.yml");
                        $groups = $pp->getGroups();
                        foreach($groups as $group){
                                if(!isset($groupsConfig[$name = $group->getName()])){
                                        $groupsConfig["chat_format"][$name]["chat"] = "&d[@rank][@level] &e<@group> &6@player: &7@message";
                                        $groupsConfig["chat_format"][$name]["display"] = "&d[@rank][@level] &e<@group> &6@player";
                                }
                        }
                        yaml_emit_file($this->getDataFolder() . "chat_format.yml", $groupsConfig);
                }
        }

        public function pruneGroupsConfig(){
                $pp = $this->getServer()->getPluginManager()->getPlugin("PurePerms");

                if($pp instanceof PurePerms){
                        $groupsConfig = yaml_parse_file($this->getDataFolder() . "chat_format.yml");
                        foreach($groupsConfig as $key => $value){
                                if($pp->getGroup($key) !== null){
                                        unset($groupsConfig["chat_format"][$key]);
                                }
                        }
                        yaml_emit_file($this->getDataFolder() . "chat_format.yml", $groupsConfig);
                }
        }

        /**
         * @return PrestigeSocietyCore
         */
        public static function getInstance(): PrestigeSocietyCore{
                return self::$instance;
        }

        /**
         * @param CommandSender       $sender
         * @param Translatable|string $message
         *
         * @return bool
         */
        public function sendMessage(CommandSender $sender, Translatable|string $message): bool{
                if($sender instanceof ConsoleCommandSender && !$this->getConfig()->get("log_spam")){
                        return false;
                }
                $sender->sendMessage($message);
                return true;
        }

        /**
         * @param Player $player
         *
         * @return bool
         */
        public function isInLobby(Player $player): bool{
                return isset($this->in_lobby[spl_object_hash($player)]);
        }

        /**
         * @param Player $player
         * @param bool   $bool
         */
        public function setIsInLobby(Player $player, bool $bool = true){
                $this->in_lobby[spl_object_hash($player)] = $bool;
        }

        /**
         * @return array
         */
        #[Pure] public function getMessages(): array{
                return $this->messages->getAll();
        }

        /**
         * @param string $from
         * @param string $message
         *
         * @return string|string[]|null
         */
        #[Pure] public function getMessage(string $from, string $message): array|string|null{
                return $this->messages->getAll()[$from][$message];
        }
}