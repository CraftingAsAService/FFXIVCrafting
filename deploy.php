<?php

namespace Deployer;

require 'recipe/laravel.php';

$user = 'nick';

// Project name
set('application', 'ffxivcrafting');
set('allow_anonymous_stats', true);
set('remote_user', $user);

// Project repository
set('repository', 'git@github.com:CraftingAsAService/FFXIVCrafting.git');

set('default_stage', 'production');
set('deploy_path', '/srv/www/{{application}}');

// Overrides branch by using --branch
//  Production will only use the `production` branch
set('branch', function() {
	return input()->getOption('branch') ?: 'master';
});

set('git_tty', true);
set('git_cache', false); // Seems to be faster without it

set('http_user', 'www-data');
set('http_group', 'www-data');

// Hosts
host('cactuar')
	->setRemoteUser($user)
    ->set('labels', ['stage' => 'production']);

// Tasks

desc('Upload the database');
task('upload:db', function() {
	upload('../cactuar/caas.sql', '~/caas.sql');
});

desc('Update the database on Cactuar');
task('cactuar:db', function() {
	require __DIR__ . '/vendor/autoload.php';
	$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__, '.env.' . get('labels')['stage']);
	$dotenv->load();
	run('mysql --defaults-file=~/password.cnf -u ' . $_ENV['DB_USERNAME'] . ' -h ' . $_ENV['DB_HOST'] . ' ' . $_ENV['DB_DATABASE'] . ' < ~/caas.sql');
});
before('cactuar:db', 'upload:db');

desc('Update the assets on Cactuar');
task('cactuar:assets', function() {
	upload('../assets/ffxiv/i/', '/srv/www/assets/i/');
});

desc('Upload env file');
task('upload:env', function() {
	upload('.env.production', '{{deploy_path}}/shared/.env');
});

// Additional task executions

// If the deployment fails, unlock it for future deployments
after('deploy:failed', 'deploy:unlock');
