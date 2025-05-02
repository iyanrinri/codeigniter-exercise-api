<?php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    /**
     * The default database connection.
     */
    public $defaultGroup = 'default';

    /**
     * The default database connection.
     */
    public $default = [
        'DSN'      => '',
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'ci_api',
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => true,
        'charset'  => 'utf8',
        'DBCollat' => 'utf8_general_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 3306,
    ];

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Set database credentials from environment variables
        $this->default['hostname'] = env('database.default.hostname') ?: $this->default['hostname'];
        $this->default['username'] = env('database.default.username') ?: $this->default['username'];
        $this->default['password'] = env('database.default.password') ?: $this->default['password'];
        $this->default['database'] = env('database.default.database') ?: $this->default['database'];
        $this->default['port'] = (int)(env('database.default.port') ?: $this->default['port']);
    }
}
