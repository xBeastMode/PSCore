<?php
namespace PrestigeSociety\Auth;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use PrestigeSociety\Auth\Handle\Sessions;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class AuthListener implements Listener{
        /** @var PrestigeSocietyCore */
        protected $core;

        /** @var Player[] */
        private $confirmation = [];

        /**
         * AuthListener constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param PlayerJoinEvent $event
         */
        public function onJoin(PlayerJoinEvent $event){
                $p = $event->getPlayer();
                if((bool) $this->core->getConfig()->get('auth')['enable']){
                        $p->setImmobile(true);
                        $this->core->module_loader->auth->addLoginSession($p);
                        if($this->core->module_loader->auth->isRegistered($p)){
                                $msg = RandomUtils::colorMessage($this->core->getMessage("login", "join_message"));
                                $msg = RandomUtils::authTextReplacer($msg, $p);
                                $p->sendMessage($msg);
                        }else{
                                $msg = RandomUtils::colorMessage($this->core->getMessage("register", "join_message"));
                                $msg = RandomUtils::authTextReplacer($msg, $p);
                                $p->sendMessage($msg);
                        }
                }
        }

        /**
         * @param BlockPlaceEvent $e
         */
        public function onPlace(BlockPlaceEvent $e){
                $p = $e->getPlayer();
                if(Sessions::isUnAuthed($p)){
                        $e->setCancelled();
                }
        }

        /**
         * @param BlockBreakEvent $e
         */
        public function onBreak(BlockBreakEvent $e){
                $p = $e->getPlayer();
                if(Sessions::isUnAuthed($p)){
                        $e->setCancelled();
                }
        }

        /**
         *
         * @param PlayerCommandPreprocessEvent $e
         *
         * @throws \InvalidStateException
         *
         */
        public function onCMDPP(PlayerCommandPreprocessEvent $e){
                $p = $e->getPlayer();
                if(Sessions::isUnAuthed($p) and ($e->getMessage()[0] === "/")){
                        $this->core->getLogger()->notice("[" . date("r") . "] " . $p->getName() . " tried to run Commands before authenticating, but don't worry, I cancelled his message.");
                        $e->setCancelled();
                }
        }

        /**
         *
         * @param PlayerChatEvent $e
         *
         * @throws \InvalidStateException
         *
         */
        public function onChat(PlayerChatEvent $e){
                $p = $e->getPlayer();
                $msg = $e->getMessage();
                if(Sessions::isUnAuthed($p)){
                        if($this->core->module_loader->auth->isRegistered($p)){
                                if($this->core->module_loader->auth->tryAuth($p, $msg) == Auth::CORRECT_PASSWORD){
                                        $p->setImmobile(false);
                                        Sessions::removeUnAuthed($p);
                                        $this->core->getLogger()->notice("[" . date("r") . "] " . $p->getName() . " logged on successfully!");
                                        $p->sendMessage(RandomUtils::colorMessage($this->core->getMessage("login", "login_success")));
                                }elseif($this->core->module_loader->auth->tryAuth($p, $msg) == Auth::WRONG_PASSWORD){
                                        $this->core->getLogger()->notice("[" . date("r") . "] " . $p->getName() . " failed to login. I think he's trying to steal this account! :o");
                                        $p->sendMessage(RandomUtils::colorMessage($this->core->getMessage("login", "wrong_password")));
                                }else{
                                        $this->core->getLogger()->notice("[" . date("r") . "] " . "An unknown error occurred while " . $p->getName() . " was trying to login!");
                                        $p->sendMessage(RandomUtils::colorMessage($this->core->getMessage("login", "unknown_error")));
                                }
                                $e->setCancelled();
                        }else{
                                if(!isset($this->confirmation [$p->getName()])){
                                        $this->confirmation [$p->getName()] = $msg;
                                        $this->core->getLogger()->notice("[" . date("r") . "] " . $p->getName() . " is trying to register, they need to confirm their password.");
                                        $p->sendMessage(RandomUtils::colorMessage($this->core->getMessage("register", "password_confirm")));
                                }else{
                                        if($this->confirmation [$p->getName()] === $msg){
                                                $p->setImmobile(false);
                                                unset($this->confirmation[$p->getName()]);
                                                Sessions::removeUnAuthed($p);
                                                $this->core->getLogger()->notice("[" . date("r") . "] " . $p->getName() . " has registered successfully!");
                                                $p->sendMessage(RandomUtils::colorMessage($this->core->getMessage("register", "registration_success")));
                                                $this->core->module_loader->auth->registerPlayer($p, $msg);
                                        }else{
                                                $this->core->getLogger()->notice("[" . date("r") . "] " . $p->getName() . " failed to register because both passwords did not match.");
                                                $p->sendMessage(RandomUtils::colorMessage($this->core->getMessage("register", "non_matching_passwords")));
                                                unset($this->confirmation [$p->getName()]);
                                        }
                                }
                                $e->setCancelled();
                        }
                }
        }
}