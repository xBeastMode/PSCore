<?php
namespace PrestigeSociety\DataProvider;
include_once __DIR__ . '/../../../vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Query\Builder as IBuilder;
use PrestigeSociety\Core\PrestigeSocietyCore;
class DataProvider{
        /** @var Capsule */
        protected Capsule $capsule;

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * DataProvider constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $capsule = new Capsule;

                $this->core = $core;
                $this->capsule = $capsule;

                $this->boot();
        }

        public function boot(){
                $this->capsule->addConnection($this->core->getConfig()->get('data_provider'));

                $this->capsule->setAsGlobal();
                $this->capsule->bootEloquent();
        }

        /**
         * @return Builder
         */
        public function getSchema(): Builder{
                return Capsule::schema();
        }

        /**
         * @return Connection
         */
        public function getDatabase(): Connection{
                return $this->capsule->getConnection();
        }

        /**
         * @param string $table
         *
         * @return IBuilder
         */
        public function getTable(string $table): IBuilder{
              return $this->capsule->getConnection()->table($table);
        }

        /**
         * @param callable $queryCallable
         * @param callable $resultCallable
         */
        public function asyncQuery(callable $queryCallable, callable $resultCallable){
                $this->core->module_loader->async_manager->submitAsyncQuery($queryCallable, $resultCallable, $this->core->getConfig()->get('data_provider'));
        }
}