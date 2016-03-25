<?php

if (! defined('HELPVARS_AUTHOR')) {
	define('HELPVARS_AUTHOR', 'Caddis');
	define('HELPVARS_AUTHOR_URL', 'https://www.caddis.co');
	define('HELPVARS_DESC', 'Make various segment and helper variables available globally.');
	define('HELPVARS_DOCS_URL', 'https://github.com/caddis/helpvars');
	define('HELPVARS_NAME', 'Helpvars');
	define('HELPVARS_VER', '1.6.0');
}

return array(
	'author' => HELPVARS_AUTHOR,
	'author_url' => HELPVARS_AUTHOR_URL,
	'description' => HELPVARS_DESC,
	'docs_url' => HELPVARS_DOCS_URL,
	'name' => HELPVARS_NAME,
	'namespace' => 'Caddis\Helpvars',
	'version' => HELPVARS_VER
);
