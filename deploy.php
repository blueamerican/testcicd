<?php
namespace Deployer;

require 'recipe/typo3.php';
require 'contrib/rsync.php';


//** Config **

//$deployPath = '/home/www/p485699/html/t3-dep-ws';
$deployPath = '/var/www/vhosts/901046.jweiland-hosting.de/httpdocs/typo3cms/dworkshop3';
//$deployPath = 'typo3cms/dworkshop';
//$deployPathProd = '/var/www/virtual/snowowl/serve';
$deployPathProd = '/var/www/vhosts/901046.jweiland-hosting.de/httpdocs/typo3cms/dworkshop3';

// Set TYPO3 Docroot/ Webroot
set('typo3_webroot', '/public');
set('keep_releases', 5);

// Set repository not needed for rsync deployments
//set('repository', 'git@github.com:snowowl78/t3-deployment-workshop');

// rsync options
set('rsync', [
    'exclude' =>[
        'composer.json',
        'composer.lock',
        '.ddev',
        '.editorconfig',
        '.env',
        '.git',
        '.github',
        '.idea',
        '.gitignore',
        'deploy.php',
        'LICENSE',
        'README.md',
        'Workshop-Docs',
        '/public/fileadmin',
        '/public/typo3temp',
    ],
    'exclude-file' => false,
    'filter' => [],
    'filter-file' => false,
    'filter-perdir' => false,
    'flags' => 'az',
    'include' => [],
    'include-file' => false,
    'options' => ['info=progress2', 'delete-after'],
]);

set('rsync_src', __DIR__);
set('rsync_dest','{{release_path}}');


// Set up / extend options for shared/ writable
add('shared_files', [
    '.env',
//    '{{typo3_webroot}}/typo3conf/AdditionalConfiguration.php',
//    '{{typo3_webroot}}/typo3conf/LocalConfiguration.php'
]);
add('shared_dirs', [
    '{{typo3_webroot}}/fileadmin',
    '{{typo3_webroot}}/typo3temp',
    '/var/lock',
    '/var/log',
]);

add('writable_dirs', []);

set('writeable_mode', 'skip');


// ** Hosts **
// Staging
//using alias
host('stage')
    ->setLabels([
        'stage' => 'Staging'
    ])
    ->set('stageDir', 'stage')
    ->setHostname(getenv('STAGING_SSH_HOST'))
    ->setDeployPath($deployPath . '/{{stageDir}}')
    ->setRemoteUser( getenv('STAGING_SSH_USER'))
    ->setPort(getenv('STAGING_SSH_PORT'))
    //->set('http_user', getenv('STAGING_SSH_USER'))
    /*->set('deploy_path', '~/t3deployws')*/
;

/**
 * setup live host
 */
host('production')
    ->setLabels([
        'prod' => 'Production'
    ])
    ->set('stageDir', 'production')
    ->setHostname(getenv('PRODUCTION_SSH_HOST'))
    ->setDeployPath($deployPathProd . '/{{stageDir}}')
    ->setRemoteUser(getenv('PRODUCTION_SSH_USER'))
    ->setPort(getenv('STAGING_SSH_PORT'))
    //->set('http_user', getenv('STAGING_SSH_USER'))
    /*->set('deploy_path', '~/t3deployws')*/;


/** demo task
 *
 * description first
 * task definition
 */
desc('DEMO TASK showing writeln, run and get');
// then task
task('demo_task', function() {
    writeln('run ls command:');
    run('ls -al {{deploy_path}}');
    writeln('show shared_files for stage = '. get('labels')['stage'] .':');
    foreach(get('shared_files') as $file) {
        writeln($file);
    }
    writeln('releasePath set to {{release_or_current_path}}' );
    writeln('current + web Path set to "{{current_path}}" + "{{typo3_webroot}}"' );
});

/**
 * Rsync deployment task
 * set description
 * configure task
 */
desc('Prepare with Rsync deployment, without use of git and composer');
task('deploy:prepare', [
    'deploy:info',
    'deploy:setup',
    'deploy:lock',
    'deploy:release',
    'rsync',
    'deploy:shared',
//    'deploy:writable'
]);

desc('Deploy customized');
task('deploy', [
    'deploy:prepare',
//   'deploy:vendors',
    'deploy:publish'
]);



// Hooks
after('deploy:release', 'rsync:warmup');
after('deploy:failed', 'deploy:unlock');
