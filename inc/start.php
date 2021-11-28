<?php

require_once __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL & ~E_DEPRECATED);

// ActiveRecord\Config::initialize(function($cfg)
// {
//     $cfg->set_model_directory('models');
//     $cfg->set_connections([
//         'development' => 'mysql://holorick:@localhost/holorick',
//         'production' => 'mysql://holorick:@holorick/holorick'
//     ]);
//     // $cfg->set_default_connection('production');
// });