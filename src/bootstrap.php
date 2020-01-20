<?php

if (is_file($autoload = __DIR__ . '/../vendor/autoload.php')) {
    require_once($autoload);
}else {
    fwrite(STDERR,
        'Querymel cannot work if it doesn\'t have the good dependencies. Run the following command:'.PHP_EOL.
        'composer install'.PHP_EOL.
        'It will install all you need to run Querymel.'.PHP_EOL
    );
    exit(1);
}
