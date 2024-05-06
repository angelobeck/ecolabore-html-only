<?php

$startTime = microtime(true);
ob_start();

define('SYSTEM_CONFIG_FILE', 'config.php');
!is_file(__DIR__ . '/' . SYSTEM_CONFIG_FILE) or include __DIR__ . '/' . SYSTEM_CONFIG_FILE;

defined('FOLDER_COMPONENTS') or define('FOLDER_COMPONENTS', 'components/');
defined('FOLDER_DATABASE') or define('FOLDER_DATABASE', 'database/');
defined('FOLDER_DOMAINS') or define('FOLDER_DOMAINS', 'domains/');
defined('FOLDER_ENGINE') or define('FOLDER_ENGINE', 'engine/');
defined('FOLDER_LIBRARY') or define('FOLDER_LIBRARY', 'library/');
defined('FOLDER_PROFILES') or define('FOLDER_PROFILES', 'profiles/');
defined('FOLDER_SHARED') or define('FOLDER_SHARED', 'shared/');
defined('FOLDER_TEMPLATES') or define('FOLDER_TEMPLATES', 'templates/');

define('PATH_ROOT', __DIR__ . '/');
define('PATH_COMPONENTS', __DIR__ . '/' . FOLDER_COMPONENTS);
define('PATH_DATABASE', __DIR__ . '/' . FOLDER_DATABASE);
define('PATH_DOMAINS', __DIR__ . '/' . FOLDER_DOMAINS);
define('PATH_ENGINE', __DIR__ . '/' . FOLDER_ENGINE);
define('PATH_LIBRARY', __DIR__ . '/' . FOLDER_LIBRARY);
define('PATH_PROFILES', __DIR__ . '/' . FOLDER_PROFILES);
define('PATH_SHARED', __DIR__ . '/' . FOLDER_SHARED);
define('PATH_TEMPLATES', __DIR__ . '/' . FOLDER_TEMPLATES);

defined('SYSTEM_HOSTING_MODE') or define('SYSTEM_HOSTING_MODE', 'single'); // 'single' | 'subfolders' | 'subdomains'
defined('SYSTEM_HOST') or define('SYSTEM_HOST', 'localhost');
defined('SYSTEM_DEFAULT_DOMAIN_NAME') or define('SYSTEM_DEFAULT_DOMAIN_NAME', 'admin');
defined('SYSTEM_ADMIN_URI') or define('SYSTEM_ADMIN_URI', 'admin');
defined('SYSTEM_PROFILES_URI') or define('SYSTEM_PROFILES_URI', 'profiles');
defined('SYSTEM_DEFAULT_LANGUAGE') or define('SYSTEM_DEFAULT_LANGUAGE', 'pt');
defined('SYSTEM_DEFAULT_CHARSET') or define('SYSTEM_DEFAULT_CHARSET', 'UTF-8');
defined('SYSTEM_SESSION_TTL') or define('SYSTEM_SESSION_TTL', 3600);
defined('SYSTEM_SESSION_CACHE_EXPIRE') or define('SYSTEM_SESSION_CACHE_EXPIRE', 300);
defined('SYSTEM_TIME_LIMIT') or define('SYSTEM_TIME_LIMIT', 6);
defined('SYSTEM_TIMEZONE') or define('SYSTEM_TIMEZONE', 'America/Sao_Paulo');
defined('SYSTEM_DISPLAY_ERRORS') or define('SYSTEM_DISPLAY_ERRORS', 1);
defined('SYSTEM_LOG_ERRORS') or define('SYSTEM_LOG_ERRORS', 0);
defined('SYSTEM_HTTPS_REDIRECT') or define('SYSTEM_HTTPS_REDIRECT', false);
defined('SYSTEM_REWRITE_ENGINE') or define('SYSTEM_REWRITE_ENGINE', false);

define('SYSTEM_SCRIPT_PATH', __FILE__);
define('SYSTEM_SCRIPT_NAME', substr(__FILE__, 1 + strlen(__DIR__)));
define('SYSTEM_COMPILER_HALT_OFFSET', __COMPILER_HALT_OFFSET__);
define('SYSTEM_LOG_FILE', __DIR__ . '/.log_php_errors');

defined('CHR_FNS') or define('CHR_FNS', '+');
define('CRLF', chr(13) . chr(10));
define('QUOT', '"');
define('TIME', time());

// PHP settings
if (SYSTEM_TIME_LIMIT)
    set_time_limit(SYSTEM_TIME_LIMIT);
session_cache_expire(SYSTEM_SESSION_CACHE_EXPIRE);
error_reporting(E_ALL);
date_default_timezone_set(SYSTEM_TIMEZONE);
ini_set('display_errors', SYSTEM_DISPLAY_ERRORS);
ini_set('log_errors', SYSTEM_LOG_ERRORS);
ini_set('error_log', SYSTEM_LOG_FILE);
ini_set('session.use_strict_mode', 0);

spl_autoload_register(
    function ($className) {
        global $io;
        if (substr($className, 0, 3) != "ecl") {
            return;
        }

        $parts = explode('_', $className);

        if (count($parts) == 1) {
            if (is_file(PATH_ENGINE . 'classes/' . $className . '.php')) {
                includeFile(PATH_ENGINE . 'classes/' . $className . '.php');
            }
            return;
        }

        @list ($prefix, $component) = explode('_', $className, 3);
        $engineFolder = strtolower(substr($prefix, 3));
        if (is_file(PATH_ENGINE . $engineFolder . '/' . $className . '.php'))
            includeFile(PATH_ENGINE . $engineFolder . '/' . $className . '.php');
        elseif ($prefix == "eclEngine")
            return;
        elseif (count($parts) >= 3 and is_file(PATH_LIBRARY . $parts[1] . '/' . $parts[2] . '/' . $className . '.php'))
            includeFile(PATH_LIBRARY . $parts[1] . '/' . $parts[2] . '/' . $className . '.php');
        elseif (is_file(PATH_LIBRARY . $component . '/' . $className . '.php'))
            includeFile(PATH_LIBRARY . $component . '/' . $className . '.php');
        elseif (count($parts) == 4 and is_file(PATH_LIBRARY . $parts[1] . '/' . $parts[2] . '/' . $className . '.php'))
            includeFile(PATH_LIBRARY . $parts[1] . '/' . $parts[2] . '/' . $className . '.php');
        else {
            if (isset ($io->log))
                $io->log->addMessage($className . ' not found', 'autoload');
            eval ('class ' . $className . ' extends ' . $prefix . '{ public $is_phantom = true; }');
        }
    }
);

function includeFile(string $path): void
{
    $ob_length = ob_get_length();
    include $path;
    if (ob_get_length() > $ob_length)
        throw new Exception('Vazamento de caracteres em ' . $path, 6);
}

// Input and output drivers
$io = new eclEngine_io();

// Data managers
$store = new eclEngine_store();

// Applications tre
$system = new eclEngine_application();

// The document
$document = new eclEngine_document();
$document->route($io->request->pathway);
$document->sessionStart();
$document->dispatch();
$document->render();

/*
if (!$document->application->ignoreSession())
    $io->session->save();

$store->close();
$io->close();

$io->request->giveBack($document);
*/

print $document->buffer;

ob_end_flush();

__halt_compiler();