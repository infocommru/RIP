<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', filter_var(getenv('YII2_DEBUG'), FILTER_VALIDATE_BOOLEAN));
defined('YII_ENV') or define('YII_ENV', getenv('YII2_ENV'));

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
