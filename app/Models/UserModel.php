<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'mcq_tbl_user';
    protected $primaryKey       = 'UserID';
    protected $returnType       = 'array';
    protected $useAutoIncrement = false; // preserve the existing dump until schema is confirmed
    protected $allowedFields    = ['UserName', 'Password', 'RollID'];
    protected $protectFields    = true;
    protected $skipValidation   = true;
}
