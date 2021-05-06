<?php
namespace core\lib;

use core\config;
use Medoo\Medoo;

class model extends Medoo {

    /**
     * @var model
     */
    private static  $instance = null;

    public static function getInstance(): self {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
     function __construct()
    {

        $arr = config::all('database');
        parent::__construct($arr);

    }
}
