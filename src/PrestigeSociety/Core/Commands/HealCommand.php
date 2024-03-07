<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\particle\HeartParticle;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Task\ParticleCircleTask;
use PrestigeSociety\Core\Utils\RandomUtils;
class HealCommand extends CoreCommand{
        /**
         * HealCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.heal");
                parent::__construct($plugin, "heal", "Low on Health? Have a patch", RandomUtils::colorMessage("&e/heal"), []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testAll($sender)){
                        return false;
                }

                /** @var Player $sender */
                if($sender->getHealth() < $sender->getMaxHealth()){
                        $sender->setHealth($sender->getMaxHealth());
                        $sender->sendTip(RandomUtils::colorMessage("&l&8» &aHEALTH SET FULL"));

                        RandomUtils::playSound("random.levelup", $sender, 500, 1, true);

                        $this->core->getScheduler()->scheduleRepeatingTask(new ParticleCircleTask($this->core, $sender, new HeartParticle()), 1);
                }
                return true;
        }
}