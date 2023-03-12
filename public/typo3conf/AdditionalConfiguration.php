<?php


$context = \TYPO3\CMS\Core\Core\Environment::getContext()->isProduction() ? 'production' : 'development';
$context = \TYPO3\CMS\Core\Core\Environment::getContext()->isTesting() ? 'testing' : $context;
$subContext = explode('/', \TYPO3\CMS\Core\Core\Environment::getContext()->__toString());
$context .= isset($subContext[1]) ? '-' . strtolower($subContext[1]) : '';

try {
    $dotenv = \Dotenv\Dotenv::createUnsafeMutable(dirname(__DIR__,2) . '/');
    $dotenv->load();
} catch (\Dotenv\Exception\InvalidPathException $exception) {
        printf("[WARNING]: Missing dotenv file -> %s \n", $exception->getMessage());
}


// Database Credentials
$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['host'] = getenv('TYPO3_DB_HOST');
$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['user'] = getenv('TYPO3_DB_USERNAME');
$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['password'] = getenv('TYPO3_DB_PASSWORD');
$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['dbname'] = getenv('TYPO3_DB_DATABASE');
$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['port'] = getenv('TYPO3_DB_PORT');

// Graphics
$GLOBALS['TYPO3_CONF_VARS']['GFX']['processor'] = getenv('TYPO3_GFX_PROCESSOR');
$GLOBALS['TYPO3_CONF_VARS']['GFX']['processor_path'] = getenv('TYPO3_GFX_PROCESSOR_PATH');
$GLOBALS['TYPO3_CONF_VARS']['GFX']['processor_path_lzw'] = getenv('TYPO3_GFX_PROCESSOR_PATH_LZW');

// Mail
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport'] = getenv('TYPO3_MAIL_TRANSPORT');
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_smtp_server'] = getenv('TYPO3_MAIL_TRANSPORT_SMTP_SERVER');
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_sendmail_command'] = getenv('TYPO3_MAIL_TRANSPORT_SENDMAIL_COMMAND');

// System
$GLOBALS['TYPO3_CONF_VARS']['SYS']['trustedHostsPattern'] = getenv('TYPO3_TRUSTED_HOSTS_PATTERN');
$GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] = getenv('TYPO3_SITENAME') ? '[' . $context . '] | '. getenv('TYPO3_SITENAME') : '[' . $context . '] TYPO3 11 Test Site';
$GLOBALS['TYPO3_CONF_VARS']['BE']['installToolPassword'] = getenv('TYPO3_INSTALL_TOOL');

# ErrorLevel -> deactivate Deprecation warnings see also https://www.php.net/manual/en/errorfunc.constants.php
#$GLOBALS['TYPO3_CONF_VARS']['SYS']['exceptionalErrors'] = 12290; // may show thrown deprecation in bootstrap package
$GLOBALS['TYPO3_CONF_VARS']['SYS']['exceptionalErrors'] = E_WARNING | E_USER_ERROR | E_RECOVERABLE_ERROR /*| E_DEPRECATED | E_USER_DEPRECATED*/;
