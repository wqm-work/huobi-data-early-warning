<?php
namespace core\config;
use MillionMile\GetEnv\Env;

return [
   "database_type"=>"mysql",
   "database_name"=>Env::get('DATABASE_NAME')?:'test',
    "server"=>Env::get('DATABASE_HOST')?:"localhost",
    "username"=>Env::get('DATABASE_USERNAME')?:"root",
    "password"=>Env::get('DATABASE_PASSWORD')?:"mysql",
    "charset"=>"utf8"
];
