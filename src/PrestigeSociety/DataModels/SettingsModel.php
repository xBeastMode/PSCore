<?php
namespace PrestigeSociety\DataModels;
include_once __DIR__ . '/../../../vendor/autoload.php';
use Illuminate\Database\Eloquent\Model;
class SettingsModel extends Model{
        protected $primaryKey = 'name';
        protected $table = 'settings';
        protected $fillable = ['name', 'settings'];
}