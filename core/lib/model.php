<?php
namespace core\lib;

use core\config;
use Medoo\Medoo;

class model extends Medoo {

    /**
     * @var model
     */
    private static ?self $instance = null;

    public static function getInstance(): self {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    private function __construct()
    {

        $arr = config::all('database');
        parent::__construct($arr);

    }
}
