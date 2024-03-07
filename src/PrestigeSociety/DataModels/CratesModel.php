<?php
namespace PrestigeSociety\DataModels;
include_once __DIR__ . '/../../../vendor/autoload.php';
use Illuminate\Database\Eloquent\Model;
class CratesModel extends Model{
        protected $table = 'crates';
        protected $fillable = ['name', 'basic_crate', 'op_crate', 'exclusive_crate', 'vote_crate', 'weapon_crate'];
}