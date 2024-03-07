<?php
namespace PrestigeSociety\DataModels;
use Illuminate\Database\Eloquent\Model;
class KitsCooldownModel extends Model{
        protected $table = 'kits_cooldown';
        protected $fillable = ['name', 'kit', 'time_claimed', 'cooldown', 'contents'];
}