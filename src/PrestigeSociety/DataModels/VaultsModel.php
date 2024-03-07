<?php
namespace PrestigeSociety\DataModels;
use Illuminate\Database\Eloquent\Model;
class VaultsModel extends Model{
        protected $table = 'vaults';
        protected $primaryKey = 'id';
        protected $fillable = ['name', 'vaultId', 'contents'];
}