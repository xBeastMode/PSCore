<?php
namespace PrestigeSociety\Chat;
use _64FF00\PurePerms\PurePerms;
use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;
use pocketmine\scheduler\TaskHandler;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Task\UnmutePlayerTask;
use PrestigeSociety\Core\Utils\StringUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
use xBeastMode\Clans\Main;
class Chat{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;
        /** @var array */
        protected mixed $chat_format;
        /** @var int[] */
        protected array $chat_time = [];

        /** @var int|TaskHandler[][]|string */
        protected string|int|array $mute_sessions = [];

        /**
         * Chat constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
                $this->chat_format = yaml_parse_file($core->getDataFolder() . "chat_format.yml");

                $this->core->getServer()->getPluginManager()->registerEvents(new ChatListener($this->core), $this->core);
        }

        public function reloadChatFormat(){
                $this->chat_format = yaml_parse_file($this->core->getDataFolder() . "chat_format.yml");
        }

        /**
         * @param Player $player
         *
         * @return bool
         */
        #[Pure] public function isMuted(Player $player): bool{
                return isset($this->mute_sessions[$player->getName()]);
        }

        /**
         * @param Player $player
         *
         * @return int
         */
        #[Pure] public function getMuteSeconds(Player $player): int{
                return $this->isMuted($player) ? $this->mute_sessions[$player->getName()][1] : 0;
        }

        /**
         * @param Player $player
         *
         * @return string
         */
        #[Pure] public function getMuteReason(Player $player): string{
                return $this->isMuted($player) ? $this->mute_sessions[$player->getName()][2] : "";
        }

        /**
         * @param Player $player
         * @param int    $seconds
         * @param string $reason
         *
         * @return bool
         */
        public function mutePlayer(Player $player, int &$seconds = 60, string &$reason = ""): bool{
                $event = $this->core->module_loader->events->onMutePlayer($player, $seconds, $reason);

                if(!$event->isCancelled()){
                        $seconds = $event->getTime();
                        $reason = $event->getReason();

                        if($this->isMuted($player)) $this->unMutePlayer($player);
                        $handler = null;
                        if($seconds > 0){
                                $handler = $this->core->getScheduler()->scheduleDelayedTask(new UnmutePlayerTask($this->core, $player), $seconds * 20);
                        }
                        $this->mute_sessions[$player->getName()] = [$handler, $seconds, $reason];

                        return true;
                }
                return false;
        }

        /**
         * @param Player $player
         *
         * @return bool
         */
        public function unMutePlayer(Player $player): bool{
                $event = $this->core->module_loader->events->onUnMutePlayer($player);
                if($this->isMuted($player) && !$event->isCancelled()){
                        $handler = $this->mute_sessions[$player->getName()][0];
                        if($handler !== null) $handler->cancel();
                        unset($this->mute_sessions[$player->getName()]);

                        return true;
                }
                return false;
        }

        /**
         * @param string $player
         *
         * @return bool
         */
        public function unMuteOfflinePlayer(string $player): bool{
                $event = $this->core->module_loader->events->onUnMuteOfflinePlayer($player);
                if(isset($this->mute_sessions[strtolower($player)]) && !$event->isCancelled()){
                        $handler = $this->mute_sessions[strtolower($player)][0];
                        if($handler !== null) $handler->cancel();
                        unset($this->mute_sessions[strtolower($player)]);

                        return true;
                }
                return false;
        }

        /**
         * @param Player $player
         * @param int    $cooldown
         *
         * @return bool
         */
        public function filterSpam(Player $player, int $cooldown = 1): bool{
                $event = $this->core->module_loader->events->onFilterSpam($player, $cooldown);
                if(!$event->isCancelled()){
                        $cooldown = $event->getCooldown();
                        if(!isset($this->chat_time[spl_object_hash($player)])){
                                $this->chat_time[spl_object_hash($player)] = time();
                                return false;
                        }

                        $isSpam = (time() - $this->chat_time[spl_object_hash($player)]) <= $cooldown;
                        $this->chat_time[spl_object_hash($player)] = time();

                        return $isSpam;
                }
                return false;
        }

        /**
         * @param Player $player
         *
         * @return string
         */
        public function formatDisplayName(Player $player): string{
                $group = "unknown";

                $kills = $this->core->module_loader->levels->getKills($player);
                $deaths = $this->core->module_loader->levels->getDeaths($player);
                $level = $this->core->module_loader->levels->getLevel($player);
                $rank = $this->core->module_loader->ranks->getRank($player);
                $pp = $player->getServer()->getPluginManager()->getPlugin("PurePerms");

                if($pp instanceof PurePerms){
                        $group = $pp->getUserDataMgr()->getGroup($player);
                        if($group !== null){
                                $group = $group->getName();
                        }
                }

                $name = $player->getName();

                if($this->core->module_loader->nicknames->hasNick($player)){
                        $name = "~" . $this->core->module_loader->nicknames->getNick($player);
                }

                $groupFormat = $this->chat_format["chat_format"][$group]["display"];
                $message = str_replace(["@kills", "@deaths", "@level", "@rank", "@group", "@player"], [$kills, $deaths, $level, $rank, $group, $name], $groupFormat);

                return RandomUtils::colorMessage($message);
        }

        /**
         * @param Player $player
         * @param string $message
         *
         * @return string
         *
         * @throws InvalidStateException
         */
        public function formatMessage(Player $player, string $message): string{
                $group = "unknown";

                $clanPlayer = Main::getInstance()->getPlayer($player);

                $kills = $this->core->module_loader->levels->getKills($player);
                $deaths = $this->core->module_loader->levels->getDeaths($player);
                $level = $this->core->module_loader->levels->getLevel($player);
                $rank = $this->core->module_loader->ranks->getRank($player);
                $pp = $player->getServer()->getPluginManager()->getPlugin("PurePerms");
                $clanName = "?";

                if($pp instanceof PurePerms){
                        $group = $pp->getUserDataMgr()->getGroup($player);
                        if($group !== null){
                                $group = $group->getName();
                        }
                }

                if(($clan = $clanPlayer->getClan()) !== null){
                        $clanName = $clan->getName();
                        $clanName = match ($clan->getRank($player)) {
                                "leader" => "***" . $clanName,
                                "coleader" => "**" . $clanName,
                                "vim" => "*" . $clanName,
                                default => $clan->getName(),
                        };
                }

                $name = $player->getName();

                if($this->core->module_loader->nicknames->hasNick($player)){
                        $name = "~" . $this->core->module_loader->nicknames->getNick($player);
                }

                if(!$player->hasPermission("chat.format")){
                        $message = StringUtils::clearColors($message);
                }

                $groupFormat = $this->chat_format["chat_format"][$group]["chat"];
                $message = str_replace([
                    "@kills",
                    "@deaths",
                    "@level",
                    "@rank",
                    "@group",
                    "@player",
                    "@message",
                    "@clan"
                ], [
                    $kills,
                    $deaths,
                    $level,
                    $rank,
                    $group,
                    $name,
                    $message,
                    $clanName
                ], $groupFormat);
                return RandomUtils::colorMessage($message);
        }
}