<?php
include_once './vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

$capsule = new Capsule;

$capsule->addConnection([
    "driver" => "mysql",
    "host" => "localhost",
    "database" => "mcdb",
    "username" => "root",
    "password" => ""
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

Capsule::schema()->create('crates', function (Blueprint $table) {
        $table->string('name')->unique()->primary();
        $table->integer('basic_crate');
        $table->integer('op_crate');
        $table->integer('exclusive_crate');
        $table->integer('vote_crate');
        $table->integer('weapon_crate');
        $table->timestamps();
});

Capsule::schema()->create('warps', function (Blueprint $table) {
        $table->increments("id");
        $table->string('name');
        $table->integer('x');
        $table->integer('y');
        $table->integer('z');
        $table->string('level');
        $table->string('owner');
        $table->timestamps();
});

class WarpsModel extends Model{
        public $incrementing = false;
        protected $primaryKey = 'id';
        protected $table = 'warps';
        protected $fillable = ['name', 'x', 'y', 'z', 'level', 'owner'];
}

$table = new WarpsModel();
$table->name = "spawn";
$table->owner = "SERVER";
$table->x = 216;
$table->y = 4;
$table->z = 238;
$table->level = "zospawn";
$table->save();

Capsule::schema()->create('homes', function (Blueprint $table) {
        $table->increments("id");
        $table->string('name');
        $table->integer('x');
        $table->integer('y');
        $table->integer('z');
        $table->string('level');
        $table->string('owner');
        $table->timestamps();
});

Capsule::schema()->create('vaults', function (Blueprint $table) {
        $table->increments("id");
        $table->string('name');
        $table->integer('vaultId');
        $table->text('contents');
        $table->timestamps();
});

Capsule::schema()->create('levels', function (Blueprint $table) {
        $table->string('name')->primary()->unique();
        $table->integer('level');
        $table->bigInteger('kills');
        $table->bigInteger('deaths');
        $table->bigInteger('blocks_broken');
        $table->bigInteger('blocks_placed');
        $table->integer('play_time')->nullable();
        $table->integer('bosses_killed')->nullable();
        $table->timestamps();
});

Capsule::schema()->create('kits_cooldown', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->string('kit');
        $table->integer('time_claimed');
        $table->integer('cooldown');
        $table->text('contents')->nullable();
        $table->timestamps();
});

Capsule::schema()->create('recovery', function (Blueprint $table) {
        $table->increments("id");
        $table->string('name');
        $table->string('item_id');
        $table->text('item_data');
        $table->timestamps();
});

Capsule::schema()->create('inventories', function (Blueprint $table) {
        $table->increments('id');
        $table->string("name");
        $table->text('inventory');
        $table->string('world');
        $table->timestamps();
});

Capsule::schema()->create('ranks', function (Blueprint $table) {
        $table->string("name")->unique()->primary();
        $table->string('rank');
        $table->timestamps();
});

Capsule::schema()->create('shop', function (Blueprint $table) {
        $table->increments('id');
        $table->string('item');
        $table->bigInteger('price')->unsigned();
        $table->integer('amount');
        $table->integer('itemId');
        $table->integer('itemMeta');
        $table->integer('category');
        $table->timestamps();
});

Capsule::schema()->create('settings', function (Blueprint $table) {
        $table->string("name")->unique()->primary();
        $table->mediumText('settings');
        $table->timestamps();
});

Capsule::schema()->create('economy', function (Blueprint $table) {
        $table->string("name")->unique()->primary();
        $table->bigInteger('money');
        $table->timestamps();
});


Capsule::schema()->create('nicknames', function (Blueprint $table) {
        $table->string("original")->unique()->primary();
        $table->string('nick');
        $table->timestamps();
});

Capsule::schema()->create('credits', function (Blueprint $table) {
        $table->string("name")->unique()->primary();
        $table->bigInteger('credits');
        $table->timestamps();
});