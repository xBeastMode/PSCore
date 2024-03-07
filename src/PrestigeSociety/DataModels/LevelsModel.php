<?php
namespace PrestigeSociety\DataModels;
include_once __DIR__ . '/../../../vendor/autoload.php';
use Illuminate\Database\Eloquent\Model;
class LevelsModel extends Model{
        protected $table = 'levels';
        protected $fillable = ['name', 'level', 'deaths', 'kills', 'blocks_broken', 'blocks_placed', 'play_time', 'bosses_killed'];
}