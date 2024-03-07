<?php
namespace PrestigeSociety\DataModels;
include_once __DIR__ . '/../../../vendor/autoload.php';
use Illuminate\Database\Eloquent\Model;
class NicksModel extends Model{
        protected $primaryKey = 'original';
        protected $table = 'nicknames';
        protected $fillable = ['original', 'nick'];
}