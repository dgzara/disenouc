<?php

// app/config/parameters.php
//include_once('/path/to/drupal/sites/default/settings.php');
//$container->setParameter('drupal.database.url', $db_url);

$container->setParameter('database_driver', 'pdo_mysql');
$container->setParameter('database_host', '127.0.0.1');
$container->setParameter('database_port', 'null');
$container->setParameter('database_name', 'sgadiseno');
$container->setParameter('database_user', 'sgadiseno');
$container->setParameter('database_password', 'QBxDy7ssNbCyG35jvYn2wTA');
$container->setParameter('mailer_transport', 'gmail');
$container->setParameter('mailer_user', 'sgadisenouc');
$container->setParameter('mailer_password', '');
$container->setParameter('locale', 'es');
$container->setParameter('secret', '15d3a5d66d8688191d7a16b64ab1a14c');
$container->setParameter('mailer_host', '127.0.0.1');

?>
