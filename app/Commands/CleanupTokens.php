<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserTokenModel;

class CleanupTokens extends BaseCommand
{
    protected $group       = 'Auth';
    protected $name       = 'auth:cleanup-tokens';
    protected $description = 'Removes expired tokens from the database';

    public function run(array $params)
    {
        $tokenModel = new UserTokenModel();
        
        // Delete expired tokens using the model
        $result = $tokenModel->where('expires_at <', date('Y-m-d H:i:s'))->delete();
        
        if ($result) {
            $affectedRows = $tokenModel->db->affectedRows();
            CLI::write("Successfully cleaned up {$affectedRows} expired tokens", 'green');
        } else {
            CLI::write('No expired tokens found', 'yellow');
        }
    }
}
