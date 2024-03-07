<?php
namespace PrestigeSociety\Events;
use pocketmine\player\Player;
use PrestigeSociety\Bosses\Entity\BossEntity;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Events\Events\Boss\BossDeathEvent;
use PrestigeSociety\Events\Events\Casino\CasinoEndSpinEvent;
use PrestigeSociety\Events\Events\Casino\CasinoStartSpinEvent;
use PrestigeSociety\Events\Events\Chat\FilterSpamEvent;
use PrestigeSociety\Events\Events\Chat\MutePlayerEvent;
use PrestigeSociety\Events\Events\Crates\CrateEndSpinEvent;
use PrestigeSociety\Events\Events\Crates\CrateStartSpinEvent;
use PrestigeSociety\Events\Events\Credits\AddCreditsEvent;
use PrestigeSociety\Events\Events\Credits\CreditsRegisterPlayerEvent;
use PrestigeSociety\Events\Events\Economy\EconomyRegisterPlayerEvent;
use PrestigeSociety\Events\Events\Credits\PayCreditsEvent;
use PrestigeSociety\Events\Events\Economy\PayMoneyEvent;
use PrestigeSociety\Events\Events\Credits\SetCreditsEvent;
use PrestigeSociety\Events\Events\Credits\SubtractCreditsEvent;
use PrestigeSociety\Events\Events\Economy\AddMoneyEvent;
use PrestigeSociety\Events\Events\Economy\SetMoneyEvent;
use PrestigeSociety\Events\Events\Economy\SubtractMoneyEvent;
use PrestigeSociety\Events\Events\Fun\ToggleFlightEvent;
use PrestigeSociety\Events\Events\Fun\ToggleFreezeEvent;
use PrestigeSociety\Events\Events\Fun\ToggleGodEvent;
use PrestigeSociety\Events\Events\Fun\ToggleHideEvent;
use PrestigeSociety\Events\Events\Fun\ToggleHideNameEvent;
use PrestigeSociety\Events\Events\Fun\ToggleHUDEvent;
use PrestigeSociety\Events\Events\Fun\ToggleIgnoreEvent;
use PrestigeSociety\Events\Events\Fun\ToggleLSDEvent;
use PrestigeSociety\Events\Events\Chat\UnMutedOfflinePlayerEvent;
use PrestigeSociety\Events\Events\Chat\UnMutePlayerEvent;
class Events{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * Events constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param BossEntity $boss_entity
         * @param Player     $killer
         * @param array      $participants
         *
         * @return BossDeathEvent
         */
        public function onBossKill(BossEntity $boss_entity, Player $killer, array $participants): BossDeathEvent{
                $event = new BossDeathEvent($boss_entity, $killer, $participants);
                $event->call();

                return $event;
        }

        /**
         * @param Player $player
         * @param int    $machine
         *
         * @return CasinoStartSpinEvent
         */
        public function onCasinoStartSpin(Player $player, int $machine): CasinoStartSpinEvent{
                $event = new CasinoStartSpinEvent($player, $machine);
                $event->call();

                return $event;
        }

        /**
         * @param Player $player
         * @param int    $machine
         * @param bool   $won
         *
         * @return CasinoEndSpinEvent
         */
        public function onCasinoEndSpin(Player $player, int $machine, bool $won): CasinoEndSpinEvent{
                $event = new CasinoEndSpinEvent($player, $machine, $won);
                $event->call();

                return $event;
        }

        /**
         * @param Player $player
         * @param int    $time
         * @param string $reason
         *
         * @return MutePlayerEvent
         */
        public function onMutePlayer(Player $player, int $time, string $reason): MutePlayerEvent{
                $event = new MutePlayerEvent($player, $time, $reason);
                $event->call();

                return $event;
        }

        /**
         * @param Player $player
         *
         * @return UnMutePlayerEvent
         */
        public function onUnMutePlayer(Player $player): UnMutePlayerEvent{
                $event = new UnMutePlayerEvent($player);
                $event->call();

                return $event;
        }

        /**
         * @param string $player
         *
         * @return UnMutedOfflinePlayerEvent
         */
        public function onUnMuteOfflinePlayer(string $player): UnMutedOfflinePlayerEvent{
                $event = new UnMutedOfflinePlayerEvent($player);
                $event->call();

                return $event;
        }

        /**
         * @param Player $player
         * @param int    $cooldown
         *
         * @return FilterSpamEvent
         */
        public function onFilterSpam(Player $player, int $cooldown): FilterSpamEvent{
                $event = new FilterSpamEvent($player, $cooldown);
                $event->call();

                return $event;
        }

        /**
         * @param Player $player
         * @param bool   $enabled
         *
         * @return ToggleLSDEvent
         */
        public function onToggleLSD(Player $player, bool $enabled): ToggleLSDEvent{
                $event = new ToggleLSDEvent($player, $enabled);
                $event->call();

                return $event;
        }

        /**
         * @param Player $player
         * @param bool   $enabled
         *
         * @return ToggleGodEvent
         */
        public function onToggleGod(Player $player, bool $enabled): ToggleGodEvent{
                $event = new ToggleGodEvent($player, $enabled);
                $event->call();

                return $event;
        }

        /**
         * @param Player $player
         * @param bool   $enabled
         *
         * @return ToggleFlightEvent
         */
        public function onToggleFlight(Player $player, bool $enabled): ToggleFlightEvent{
                $event = new ToggleFlightEvent($player, $enabled);
                $event->call();

                return $event;
        }

        /**
         * @param Player $sender
         * @param Player $player
         * @param bool   $enabled
         * @param bool   $cancel_commands
         *
         * @return ToggleFreezeEvent
         */
        public function onToggleFreeze(Player $sender, Player $player, bool $enabled, bool $cancel_commands): ToggleFreezeEvent{
                $event = new ToggleFreezeEvent($sender, $player, $enabled, $cancel_commands);
                $event->call();

                return $event;
        }

        /**
         * @param Player $player
         * @param bool   $enabled
         *
         * @return ToggleHideEvent
         */
        public function onToggleHide(Player $player, bool $enabled): ToggleHideEvent{
                $event = new ToggleHideEvent($player, $enabled);
                $event->call();

                return $event;
        }

        /**
         * @param Player $player
         * @param bool   $enabled
         *
         * @return ToggleHideNameEvent
         */
        public function onToggleHideName(Player $player, bool $enabled): ToggleHideNameEvent{
                $event = new ToggleHideNameEvent($player, $enabled);
                $event->call();

                return $event;
        }

        /**
         * @param Player $player
         * @param Player $target
         * @param bool   $enabled
         *
         * @return ToggleIgnoreEvent
         */
        public function onToggleIgnore(Player $player, Player $target, bool $enabled): ToggleIgnoreEvent{
                $event = new ToggleIgnoreEvent($player, $target, $enabled);
                $event->call();

                return $event;
        }

        /**
         * @param Player $player
         * @param bool   $enabled
         *
         * @return ToggleHUDEvent
         */
        public function onToggleHUD(Player $player, bool $enabled): ToggleHUDEvent{
                $event = new ToggleHUDEvent($player, $enabled);
                $event->call();

                return $event;
        }

        /**
         * @param Player $player
         * @param string $crate
         *
         * @return CrateStartSpinEvent
         */
        public function onCrateStartSpin(Player $player, string $crate): CrateStartSpinEvent{
                $event = new CrateStartSpinEvent($player, $crate);
                $event->call();

                return $event;
        }

        /**
         * @param Player $player
         * @param string $crate
         *
         * @return CrateEndSpinEvent
         */
        public function onCrateEndSpin(Player $player, string $crate): CrateEndSpinEvent{
                $event = new CrateEndSpinEvent($player, $crate);
                $event->call();

                return $event;
        }

        /**
         * @param string|Player $player
         *
         * @return CreditsRegisterPlayerEvent
         */
        public function onCreditsRegisterPlayer(Player|string $player): CreditsRegisterPlayerEvent{
                $event = new CreditsRegisterPlayerEvent($player);
                $event->call();

                return $event;
        }

        /**
         * @param string|Player $player
         * @param int           $credits
         *
         * @return SetCreditsEvent
         */
        public function onSetCredits(Player|string $player, int $credits): SetCreditsEvent{
                $event = new SetCreditsEvent($player, $credits);
                $event->call();

                return $event;
        }

        /**
         * @param string|Player $player
         * @param int           $credits
         *
         * @return AddCreditsEvent
         */
        public function onAddCredits(Player|string $player, int $credits): AddCreditsEvent{
                $event = new AddCreditsEvent($player, $credits);
                $event->call();

                return $event;
        }

        /**
         * @param string|Player $player
         * @param int           $credits
         *
         * @return SubtractCreditsEvent
         */
        public function onSubtractCredits(Player|string $player, int $credits): SubtractCreditsEvent{
                $event = new SubtractCreditsEvent($player, $credits);
                $event->call();

                return $event;
        }

        /**
         * @param string|Player $player
         * @param string|Player $target
         * @param int           $credits
         *
         * @return PayCreditsEvent
         */
        public function onPayCredits(Player|string $player, Player|string $target, int $credits): PayCreditsEvent{
                $event = new PayCreditsEvent($player, $target, $credits);
                $event->call();

                return $event;
        }

        /**
         * @param string|Player $player
         *
         * @return EconomyRegisterPlayerEvent
         */
        public function onEconomyRegisterPlayer(Player|string $player): EconomyRegisterPlayerEvent{
                $event = new EconomyRegisterPlayerEvent($player);
                $event->call();

                return $event;
        }

        /**
         * @param string|Player $player
         * @param int           $money
         *
         * @return SetMoneyEvent
         */
        public function onSetMoney(Player|string $player, int $money): SetMoneyEvent{
                $event = new SetMoneyEvent($player, $money);
                $event->call();

                return $event;
        }

        /**
         * @param string|Player $player
         * @param int           $money
         *
         * @return AddMoneyEvent
         */
        public function onAddMoney(Player|string $player, int $money): AddMoneyEvent{
                $event = new AddMoneyEvent($player, $money);
                $event->call();

                return $event;
        }

        /**
         * @param string|Player $player
         * @param int           $credits
         *
         * @return SubtractMoneyEvent
         */
        public function onSubtractMoney(Player|string $player, int $credits): SubtractMoneyEvent{
                $event = new SubtractMoneyEvent($player, $credits);
                $event->call();

                return $event;
        }

        /**
         * @param string|Player $player
         * @param string|Player $target
         * @param int           $money
         *
         * @return PayMoneyEvent
         */
        public function onPayMoney(Player|string $player, Player|string $target, int $money): PayMoneyEvent{
                $event = new PayMoneyEvent($player, $target, $money);
                $event->call();

                return $event;
        }
}