<?php

// Custom htconfig.php for Docker usage.
// Uses a lot of environment variables

// Use environment variables for mysql if they are set beforehand
if (!empty(getenv('MYSQL_HOST'))
	&& !empty(getenv('MYSQL_PORT'))
	&& (!empty(getenv('MYSQL_USERNAME')) || !empty(getenv('MYSQL_USER')))
	&& !empty(getenv('MYSQL_PASSWORD'))
	&& !empty(getenv('MYSQL_DATABASE'))) {
	$db_host = getenv('MYSQL_HOST') . ':' . getenv('MYSQL_PORT');
	if (!empty(getenv('MYSQL_USERNAME'))) {
		$db_user = getenv('MYSQL_USERNAME');
	} elseif (!empty(getenv('MYSQL_USER'))) {
		$db_user = getenv('MYSQL_USER');
	}
	$db_pass = getenv('MYSQL_PASSWORD');
	$db_data = getenv('MYSQL_DATABASE');
}

// Set the database connection charset to full Unicode (utf8mb4).
// Changing this value will likely corrupt the special characters.
// You have been warned.
$a->config['system']['db_charset'] = "utf8mb4";

// Choose a legal default timezone. If you are unsure, use "America/Los_Angeles".
// It can be changed later and only applies to timestamps for anonymous viewers.

if (!empty(getenv('TZ'))) {
	$default_timezone = getenv('TZ');
} else {
	$default_timezone = 'America/Los_Angeles';
}

// Default system language
if (!empty(getenv('LANGUAGE'))) {
	$a->config['system']['language'] = getenv('LANGUAGE');
} else {
	$a->config['system']['language'] = 'en';
}

// What is your site name?
if (!empty(getenv('SITENAME'))) {
	$a->config['sitename'] = getenv('SITENAME');
} else {
	$a->config['sitename'] = "Friendica Social Network";
}

// Your choices are REGISTER_OPEN, REGISTER_APPROVE, or REGISTER_CLOSED.
// Be certain to create your own personal account before setting
// REGISTER_CLOSED. 'register_text' (if set) will be displayed prominently on
// the registration page. REGISTER_APPROVE requires you set 'admin_email'
// to the email address of an already registered person who can authorise
// and/or approve/deny the request.

// In order to perform system administration via the admin panel, admin_email
// must precisely match the email address of the person logged in.

$a->config['register_policy'] = REGISTER_OPEN;
$a->config['register_text'] = '';
if (!empty(getenv('MAILNAME'))) {
	$a->config['admin_email'] = getenv('MAILNAME');
} else {
	$a->config['admin_email'] = '';
}

// Maximum size of an imported message, 0 is unlimited

$a->config['max_import_size'] = 200000;

// maximum size of uploaded photos

$a->config['system']['maximagesize'] = 800000;

// Location of PHP command line processor

$a->config['php_path'] = 'php';

// Server-to-server private message encryption (RINO) is allowed by default.
// set to 0 to disable, 1 to enable

$a->config['system']['rino_encrypt'] = 1;

// allowed themes (change this from admin panel after installation)

$a->config['system']['allowed_themes'] = 'quattro,vier,duepuntozero,smoothly';

// default system theme

$a->config['system']['theme'] = 'vier';


// By default allow pseudonyms

$a->config['system']['no_regfullname'] = true;

//Deny public access to the local directory
//$a->config['system']['block_local_dir'] = false;

// Location of the global directory
$a->config['system']['directory'] = 'https://dir.friendica.social';

// Allowed protocols in link URLs; HTTP protocols always are accepted
$a->config['system']['allowed_link_protocols'] = ['ftp', 'ftps', 'mailto', 'cid', 'gopher'];

// Authentication cookie lifetime, in days
$a->config['system']['auth_cookie_lifetime'] = 7;

if (!empty(getenv('VALIDATION'))) {
	$a->config['system']['disable_url_validation'] = strtolower(getenv('VALIDATION'));
	$a->config['system']['disable_email_validation'] = strtolower(getenv('VALIDATION'));
}

if (!empty(getenv('CACHE_DRIVER'))) {
	$a->config['system']['cache_driver'] = strtolower(getenv('CACHE_DRIVER'));

	if (!empty(getenv('REDIS_HOST'))) {
		$a->config['system']['redis_host'] = getenv('REDIS_HOST');
	}

	if (!empty(getenv('REDIS_PORT'))) {
		$a->config['system']['redis_port'] = getenv('REDIS_PORT');
	}

	if (!empty(getenv('MEMCACHE_HOST'))) {
		$a->config['system']['memcache_host'] = getenv('MEMCACHE_HOST');
	}

	if (!empty(getenv('MEMCACHE_PORT'))) {
		$a->config['system']['memcache_port'] = getenv('MEMCACHE_PORT');
	}

	if (!empty(getenv('MEMCACHED_HOSTS'))) {
		$a->config['system']['memcached_hosts'] = getenv('MEMCACHED_HOSTS');
	}
}