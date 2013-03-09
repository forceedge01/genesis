<?php

//inside SiteMaterial folder, change name accordingly.
define('CLONE_ZIP_NAME', 'ST-wordpress.zip');

define('CLONE_SQL_NAME', 'wp_init.sql');

define('SITE_MATERIAL_FOLDER', ROOT . 'Source/SiteMaterial/');

define('SITES_FOLDER', ROOT . 'Sites/');

define('BACKUPS_FOLDER', SITES_FOLDER . 'Backups/');

define('ALLOW_ONLY_FANDIST_SUBDOMAINS', TRUE);

define('API_KEY', 'd43is4H39T8u');

define('USE_DEFAULT_API_CREDENTIALS', true);// If false, will create new API creds each time a site is created.

//same for demo.fandistribution.com - test API creds
define('DEFAULT_PORTAL_ID', '100004');

define('DEFAULT_MERCHANT_ID', '169');

define('DEFAULT_PRODUCT_ID', '38178');