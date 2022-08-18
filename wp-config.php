<?php
# Database Configuration
define( 'DB_NAME', 'wp_mariasplace32' );
define( 'DB_USER', 'mariasplace32' );
define( 'DB_PASSWORD', 'j8VwtVMubIXJoqoL5BDI' );
define( 'DB_HOST', '127.0.0.1:3306' );
define( 'DB_HOST_SLAVE', '127.0.0.1:3306' );
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_unicode_ci');
$table_prefix = 'wp_';
# Security Salts, Keys, Etc
define('AUTH_KEY',         'ncc]K0NFVc$(ZbL#k-xhNk3|>-XNO$%U)_;w11 vtw>>[%4}ssL{IVjvL)&lG%4G');
define('SECURE_AUTH_KEY',  'APSTt6<K0hEdFDTl>jibO/Qrkfp;B4,KPv9`rs+;AeO-H?e`LCC$:`+a/nK1y51i');
define('LOGGED_IN_KEY',    '8tq(X<.h>Gg_)_#+Fl2Hi(g0nf[e_w6yNJ_YQvJx>?%&sLyBI3&R>o]@(]o5~Fc2');
define('NONCE_KEY',        'Rq#u$w9(Vlga5GT]j.gP7k2q*jVeB=p;mX&C3:+O/*r jKqK?<OTA47arM[hh};h');
define('AUTH_SALT',        '-PC4:M:%~A:2b,9bqut #s~P>#!0-sfG>~28_e@cY:f2F[HOhI Qm]VU>kc45v.]');
define('SECURE_AUTH_SALT', '):KrRyO.(+3*A%?m{bvKU^xnq/yF-G3c[.uYjC+$.2CQq]y%ce!!z10od<I.T&ev');
define('LOGGED_IN_SALT',   'pXsC%Qz4^)G.tMq!o?>X7faPUQCPvGcdc<(3u*G18Q:cRQj>F><%SkOM6Stc}0if');
define('NONCE_SALT',       '(ItSfNnQ/U)VVd5v6HK2}}TnZ9q[:+>l^@]_BSzw*5m{Jl:gZq!M+{ZvGUzZGJ.');
# Localized Language Stuff
define('CONCATENATE_SCRIPTS', false );
define( 'WP_CACHE', TRUE );
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', TRUE );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_AUTO_UPDATE_CORE', false );
define( 'PWP_NAME', 'mariasplace32' );
define( 'FS_METHOD', 'direct' );
define( 'FS_CHMOD_DIR', 0775 );
define( 'FS_CHMOD_FILE', 0664 );
define( 'PWP_ROOT_DIR', '/nas/wp' );
define( 'WPE_APIKEY', '064cdcb60225f52c71c2d5a7b9fc939a03436b89' );
define( 'WPE_CLUSTER_ID', '101172' );
define( 'WPE_CLUSTER_TYPE', 'pod' );
define( 'WPE_ISP', true );
define( 'WPE_BPOD', false );
define( 'WPE_RO_FILESYSTEM', false );
define( 'WPE_LARGEFS_BUCKET', 'largefs.wpengine' );
define( 'WPE_SFTP_PORT', 2222 );
define( 'WPE_LBMASTER_IP', '' );
define( 'WPE_CDN_DISABLE_ALLOWED', false );
define( 'DISALLOW_FILE_MODS', FALSE );
define( 'DISALLOW_FILE_EDIT', FALSE );
define( 'DISABLE_WP_CRON', false );
define( 'WPE_FORCE_SSL_LOGIN', false );
define( 'FORCE_SSL_LOGIN', false );
/*SSLSTART*/ if ( isset($_SERVER['HTTP_X_WPE_SSL']) && $_SERVER['HTTP_X_WPE_SSL'] ) $_SERVER['HTTPS'] = 'on'; /*SSLEND*/
define( 'WPE_EXTERNAL_URL', false );
define( 'WP_POST_REVISIONS', 3 );
define( 'WPE_WHITELABEL', 'wpengine' );
define( 'WP_TURN_OFF_ADMIN_BAR', false );
define( 'WPE_BETA_TESTER', false );
umask(0002);
$wpe_cdn_uris=array ( );
$wpe_no_cdn_uris=array ( );
$wpe_content_regexs=array ( );
$wpe_all_domains=array ( 0 => 'dev.mariasplace.com', 1 => 'mariasplace32.wpengine.com', 2 => 'mariasplace32.wpenginepowered.com', );
$wpe_varnish_servers=array ( 0 => 'pod-101172', );
$wpe_special_ips=array ( 0 => '104.196.247.182', );
$wpe_ec_servers=array ( );
$wpe_largefs=array ( );
$wpe_netdna_domains=array ( );
$wpe_netdna_domains_secure=array ( );
$wpe_netdna_push_domains=array ( );
$wpe_domain_mappings=array ( );
$memcached_servers=array ( );

define( 'WPE_SFTP_ENDPOINT', '' );
define('WPLANG', '');
# WP Engine ID
# WP Engine Settings
//define('WP_MEMORY_LIMIT', '512M');
//define('UPLOAD_MAX_FILESIZE', '32M');
//define('POST_MAX_SIZE', '32M');
# That's It. Pencils down
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

require_once(ABSPATH . 'wp-settings.php');