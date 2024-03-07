<?php
namespace PrestigeSociety\Core\Task;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\world\particle\Particle;
use PrestigeSociety\Core\PrestigeSocietyCore;
class ParticleCircleTask extends Task{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;
        /** @var Entity */
        protected Entity $entity;
        /** @var Particle */
        protected Particle $particle;

        /** @var float */
        protected float $y = 0;
        /** @var float */
        protected float $add = 0;
        protected float $radians = 0;

        /** @var int */
        protected int $run_times = 0;
        /** @var Vector3|null */
        protected ?Vector3 $particle_position = null;

        /**
         * ParticleCircleTask constructor.
         *
         * @param PrestigeSocietyCore $core
         * @param Entity              $entity
         * @param Particle            $particle
         * @param int                 $run_times
         */
        public function __construct(PrestigeSocietyCore $core, Entity $entity, Particle $particle, int $run_times = 20){
                $this->core = $core;
                $this->entity = $entity;
                $this->particle = $particle;
                $this->run_times = $run_times;

                $this->particle_position = $entity->getPosition();
        }

        /**
         * Actions to execute when run
         *
         * @return void
         */
        public function onRun(): void{
                if($this->entity->isClosed()){
                        $this->getHandler()->cancel();
                        return;
                }

                if($this->y >= 2){
                        $this->add = -0.05;
                }elseif($this->y <= 0.1){
                        $this->add = 0.05;
                }

                $this->radians += 0.1;
                if($this->radians >= 2){
                        $this->radians = 0;
                }

                $this->y += $this->add;

                $x = cos(M_PI * $this->radians);
                $z = sin(M_PI * $this->radians);

                $this->particle_position->x = $this->entity->getLocation()->x + $x;
                $this->particle_position->y = $this->entity->getLocation()->y + $this->y;
                $this->particle_position->z = $this->entity->getLocation()->z + $z;

                $this->entity->getWorld()->addParticle($this->particle_position, $this->particle);

                if($this->run_times !== -1 && --$this->run_times <= 0){
                        $this->getHandler()->cancel();
                }
        }
}