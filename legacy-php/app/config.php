<?php
/**
 * Configuration Fraction Média.
 * SQLite fonctionne sans réglage sur la plupart des hébergeurs mutualisés.
 * Si MySQL est disponible, passez DB_DRIVER à 'mysql' et remplissez les champs.
 */
define('DB_DRIVER', 'sqlite');
define('DB_HOST', 'localhost');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '');

define('SITE_NOM', 'Fraction Média');
define('SITE_SIGLE', 'FRACTION·MÉDIA');
define('APP_DEBUG', false);
