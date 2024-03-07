<?php
namespace PrestigeSociety\Data;
//use PrestigeSociety\Auth\Command\AuthCommand;
use PrestigeSociety\Bosses\Commands\AddBossCommand;
use PrestigeSociety\Bosses\Commands\RemoveBossCommand;
use PrestigeSociety\Bosses\Commands\RespawnBossCommand;
use PrestigeSociety\Bosses\Commands\SpawnBossCommand;
use PrestigeSociety\Casino\Commands\CasinoCommand;
use PrestigeSociety\Core\Commands\BanCommand;
use PrestigeSociety\Core\Commands\ClearInventoryCommand;
use PrestigeSociety\Core\Commands\CombatTimeCommand;
use PrestigeSociety\Core\Commands\EatCommand;
use PrestigeSociety\Core\Commands\FlyCommand;
use PrestigeSociety\Core\Commands\FreezeCommand;
use PrestigeSociety\Core\Commands\GodCommand;
use PrestigeSociety\Core\Commands\HealCommand;
use PrestigeSociety\Core\Commands\HideCommand;
use PrestigeSociety\Core\Commands\HideNameCommand;
use PrestigeSociety\Core\Commands\HUDCommand;
use PrestigeSociety\Core\Commands\HurtCommand;
use PrestigeSociety\Core\Commands\IgnoreCommand;
use PrestigeSociety\Core\Commands\LSDCommand;
use PrestigeSociety\Core\Commands\MotionCommand;
use PrestigeSociety\Core\Commands\MuteCommand;
use PrestigeSociety\Core\Commands\OpHelpCommand;
//use PrestigeSociety\Core\Commands\OxygenCommand;
use PrestigeSociety\Core\Commands\ParticlesCommand;
use PrestigeSociety\Core\Commands\PlaySoundXCommand;
use PrestigeSociety\Core\Commands\BlacksmithCommand;
use PrestigeSociety\Core\Commands\RestartCommand;
use PrestigeSociety\Core\Commands\RestartTimeCommand;
use PrestigeSociety\Core\Commands\RideStickCommand;
use PrestigeSociety\Core\Commands\RPPCommand;
use PrestigeSociety\Core\Commands\RunCommand;
use PrestigeSociety\Core\Commands\RunCommandsCommand;
use PrestigeSociety\Core\Commands\SBanCommand;
use PrestigeSociety\Core\Commands\SellCommand;
use PrestigeSociety\Core\Commands\SetOnFireCommand;
use PrestigeSociety\Core\Commands\ShootProjectileCommand;
use PrestigeSociety\Core\Commands\SizeCommand;
use PrestigeSociety\Core\Commands\StackStickCommand;
use PrestigeSociety\Core\Commands\SummonCommand;
use PrestigeSociety\Core\Commands\TellRawCommand;
use PrestigeSociety\Core\Commands\ToggleCommand;
use PrestigeSociety\Core\Commands\UnmuteCommand;
use PrestigeSociety\Core\Commands\WildCommand;
use PrestigeSociety\Core\Commands\WorldCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Crates\Commands\CratesCommand;
use PrestigeSociety\Credits\Commands\AddCreditsCommand;
use PrestigeSociety\Credits\Commands\CreditsCommand;
use PrestigeSociety\Credits\Commands\SetCreditsCommand;
use PrestigeSociety\Credits\Commands\SubtractCreditsCommand;
use PrestigeSociety\CreditShop\Commands\CreditShopCommand;
use PrestigeSociety\CustomItems\Commands\CustomItemCommand;
use PrestigeSociety\Directions\DirectionsCommand;
use PrestigeSociety\Economy\Commands\AddMoneyCommand;
//use PrestigeSociety\Enchants\EnchantShardCommand;
use PrestigeSociety\Forms\Commands\FormListCommand;
use PrestigeSociety\Forms\Commands\OpenFormCommand;
use PrestigeSociety\Kits\Command\KitCommand;
use PrestigeSociety\Management\Command\ManageCommand;
use PrestigeSociety\MineResetter\Commands\MineResetterCommand;
use PrestigeSociety\PowerUps\Commands\ActivatePowerupCommand;
use PrestigeSociety\PowerUps\Commands\AddPowerupCommand;
use PrestigeSociety\Economy\Commands\BankCommand;
use PrestigeSociety\Economy\Commands\GiveCashCommand;
use PrestigeSociety\Economy\Commands\SetMoneyCommand;
use PrestigeSociety\Economy\Commands\SubtractMoneyCommand;
//use PrestigeSociety\Enchants\EnchantCommand;
//use PrestigeSociety\Enchants\RemoveEnchantCommand;
use PrestigeSociety\Hats\HatCommand;
use PrestigeSociety\LandProtector\Command\LandCommand;
use PrestigeSociety\Levels\Commands\PrestigeCommand;
use PrestigeSociety\Levels\Commands\SetDeathsCommand;
use PrestigeSociety\Levels\Commands\SetKillsCommand;
use PrestigeSociety\Levels\Commands\SetLevelCommand;
use PrestigeSociety\Levels\Commands\StatsCommand;
use PrestigeSociety\Nicknames\NickCommand;
use PrestigeSociety\Player\Commands\ProfileCommand;
use PrestigeSociety\Player\Commands\SettingsCommand;
use PrestigeSociety\PowerUps\Commands\PowerUpsCommand;
use PrestigeSociety\ProtectionStones\Commands\ProtectionStonesCommand;
use PrestigeSociety\Ranks\Commands\RankUpCommand;
use PrestigeSociety\Ranks\Commands\SeeRankCommand;
use PrestigeSociety\Ranks\Commands\SetRankCommand;
use PrestigeSociety\Recovery\Commands\RecoveryCommand;
use PrestigeSociety\Shop\Commands\AddShopCommand;
use PrestigeSociety\Shop\Commands\RemoveShopCommand;
use PrestigeSociety\Shop\Commands\ShopCommand;
//use PrestigeSociety\Spawners\Commands\SpawnersCommand;
use PrestigeSociety\Statistics\Command\StatCommand;
use PrestigeSociety\Teleport\Commands\Home\DeleteHomeCommand;
use PrestigeSociety\Teleport\Commands\Home\HomeCommand;
use PrestigeSociety\Teleport\Commands\Home\MaxHomesCommand;
use PrestigeSociety\Teleport\Commands\Home\SetHomeCommand;
use PrestigeSociety\Teleport\Commands\Spawn\SetSpawnCommand;
use PrestigeSociety\Teleport\Commands\Spawn\SpawnCommand;
use PrestigeSociety\Teleport\Commands\Teleport\BackCommand;
use PrestigeSociety\Teleport\Commands\Teleport\TpaCommand;
use PrestigeSociety\Teleport\Commands\Teleport\TpHereCommand;
use PrestigeSociety\Teleport\Commands\Warp\DeleteWarpCommand;
use PrestigeSociety\Teleport\Commands\Warp\InstantWarpCommand;
use PrestigeSociety\Teleport\Commands\Warp\SetWarpCommand;
use PrestigeSociety\Teleport\Commands\Warp\WarpCommand;
use PrestigeSociety\Teleport\Commands\Warp\WarpInfoCommand;
use PrestigeSociety\Vaults\UnlockVaultCommand;
use PrestigeSociety\Vaults\VaultCommand;
use PrestigeSociety\Warzone\Command\AddZoneCommand;
use PrestigeSociety\Warzone\Command\DespawnLootCrateCommand;
use PrestigeSociety\Warzone\Command\RemoveZoneCommand;
use PrestigeSociety\Warzone\Command\RespawnLootCrateCommand;
class CommandLoader{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * CommandLoader constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }
        
        public function loadCommands(){
                $commandMap = $this->core->getServer()->getCommandMap();
                $command = $commandMap->getCommand("ban");
                $command->setLabel("ban_disabled");
                $command->unregister($commandMap);

                $this->core->getServer()->getCommandMap()->register("pscore", new OpHelpCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new EatCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new LandCommand($this->core));
                //$this->core->getServer()->getCommandMap()->register("pscore", new AuthCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new VaultCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new BlacksmithCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new WorldCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new FlyCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SetDeathsCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SetKillsCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SetLevelCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new StatsCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new AddMoneyCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new BankCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SubtractMoneyCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SetMoneyCommand($this->core));

                $this->core->getServer()->getCommandMap()->register("pscore", new AddCreditsCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new CreditsCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SetCreditsCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SubtractCreditsCommand($this->core));

                $this->core->getServer()->getCommandMap()->register("pscore", new SBanCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new BanCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new AddShopCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new RemoveShopCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new ShopCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new LSDCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new RPPCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new RankUpCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new GodCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new NickCommand($this->core));
                //$this->core->getServer()->getCommandMap()->register("pscore", new EnchantCommand($this->core));
                //$this->core->getServer()->getCommandMap()->register("pscore", new EnchantShardCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new ClearInventoryCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SellCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new HUDCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SetRankCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SeeRankCommand($this->core));

                $this->core->getServer()->getCommandMap()->register("pscore", new ProtectionStonesCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new StatCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new CombatTimeCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new RestartTimeCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new RestartCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SettingsCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new PrestigeCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new AddBossCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new RemoveBossCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SpawnBossCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new ProfileCommand($this->core));
                //$this->core->getServer()->getCommandMap()->register("pscore", new RemoveEnchantCommand($this->core));
                //$this->core->getServer()->getCommandMap()->register("pscore", new SpawnersCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new HealCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new HurtCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new HideCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new FreezeCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new HideNameCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new RunCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new MotionCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new CreditShopCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new UnlockVaultCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new MuteCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new UnmuteCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new IgnoreCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new DirectionsCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new HatCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new StackStickCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new RideStickCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new RespawnBossCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SummonCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new GiveCashCommand($this->core));

                $this->core->getServer()->getCommandMap()->register("pscore", new ShootProjectileCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new RunCommandsCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SetOnFireCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new AddPowerupCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new PowerUpsCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new CasinoCommand($this->core));
                //$this->core->getServer()->getCommandMap()->register("pscore", new OxygenCommand($this->core));

                $this->core->getServer()->getCommandMap()->register("pscore", new WildCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new ToggleCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SizeCommand($this->core));

                $this->core->getServer()->getCommandMap()->register("pscore", new CratesCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new CustomItemCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new OpenFormCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new FormListCommand($this->core));

                $this->core->getServer()->getCommandMap()->register("pscore", new ManageCommand($this->core));

                // teleport essentials
                $this->core->getServer()->getCommandMap()->register("pscore", new HomeCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SetHomeCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new DeleteHomeCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new MaxHomesCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SpawnCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SetSpawnCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new WarpCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new SetWarpCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new DeleteWarpCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new BackCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new TpaCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new TpHereCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new InstantWarpCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new WarpInfoCommand($this->core));

                $this->core->getServer()->getCommandMap()->register("pscore", new ActivatePowerupCommand($this->core));

                $this->core->getServer()->getCommandMap()->register("pscore", new KitCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new TellRawCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new RecoveryCommand($this->core));

                $this->core->getServer()->getCommandMap()->register("pscore", new AddZoneCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new RemoveZoneCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new RespawnLootCrateCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new DespawnLootCrateCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new MineResetterCommand($this->core));

                // vanilla commands
                $this->core->getServer()->getCommandMap()->register("pscore", new ParticlesCommand($this->core));
                $this->core->getServer()->getCommandMap()->register("pscore", new PlaySoundXCommand($this->core));
        }
}