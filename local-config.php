<?php
// Note: Database constants are set in the automatically-generated
// local-config-db.php. Change these via your config.local.yaml instead.

// Loopback connections can suck, disable if you don't need cron
# define( 'DISABLE_WP_CRON', true );

// You'll probably want Automatic Updates disabled during development
define( 'AUTOMATIC_UPDATER_DISABLED', true );

// You'll probably want debug logging during development
define( 'WP_DEBUG_LOG', true );

// Set JetPack to debug also
define( 'JETPACK_DEV_DEBUG', true );

// Set up SSL extension
// (live site uses SSL; this makes it easier to import and export the db)
// define('WP_SITEURL', 'https://' . $_SERVER['HTTP_HOST'] . '/wp');
// define('WP_HOME', 'https://' . $_SERVER['HTTP_HOST']);
