<?php
namespace PrestigeSociety\Chat;
use Exception;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class ChatListener implements Listener{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * ChatListener constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param PlayerChatEvent $event
         *
         * @throws Exception
         */
        public function onPlayerChat(PlayerChatEvent $event){
                $player = $event->getPlayer();
                $message = $event->getMessage();

                if($this->core->module_loader->chat->isMuted($player)){
                        $seconds = $this->core->module_loader->chat->getMuteSeconds($player);
                        $reason = $this->core->module_loader->chat->getMuteReason($player);

                        $message = RandomUtils::colorMessage($this->core->getMessage("chat_protector", "still_muted"));
                        $message = str_replace(["@seconds", "@reason"], [$seconds, $reason === "" ? "N/A" : $reason], $message);
                        $player->sendMessage($message);

                        $event->cancel();
                }

                if($this->core->module_loader->chat->filterSpam($event->getPlayer(), intval($this->core->getConfig()->getAll()["anti_spam"]["time"])) and !$player->hasPermission("spam.bypass")){
                        $this->core->getLogger()->notice("[" . date("r") . "]" . $player->getName() . " tried to spam, but don't worry, I cancelled his message.");
                        $player->sendMessage(RandomUtils::colorMessage($this->core->getMessage("chat_protector", "no_spam")));
                        $event->cancel();
                }


                $format = $this->core->module_loader->chat->formatMessage($player, $message);
                $event->setFormat($format);
        }
}