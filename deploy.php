<?php

namespace Deployer;

require 'recipe/laravel.php';

// Server user set in ~/.aliases
$user = 'nick';

// Project name
set('application', 'ffxivcrafting');
set('allow_anonymous_stats', true);

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

set('shared_dirs', [
	'storage',
	'public/assets',
]);

set('shared_files', [
	'.env',
]);

set('copy_dirs', [
	'vendor',
]);

set('writable_dirs', [
	'bootstrap/cache',
	'storage',
	'storage/app',
	'storage/app/public',
	'storage/framework',
	'storage/framework/cache',
	'storage/framework/sessions',
	'storage/framework/views',
	'storage/logs',
	'vendor',
]);

// Hosts

host('qa')
	->hostname('ultros')
	->identityFile('~/.ssh/id_rsa')
	->user($user)
	->forwardAgent() // Use local ssh credentials for git
	->stage('qa')
	->set('deploy_path', '/srv/www/{{application}}-qa');

host('production')
	->hostname('ultros')
	->identityFile('~/.ssh/id_rsa')
	->user($user)
	->forwardAgent() // Use local ssh credentials for git
	->stage('production');

// Tasks

desc('Upload the database');
task('upload:db', function() {
	upload('../ultros/caas.sql', '~/caas.sql');
});

desc('Update the database on Ultros');
task('ultros:db', function() {
	require __DIR__ . '/vendor/autoload.php';
	$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__, '.env.' . get('stage'));
	$dotenv->load();
	run('mysql --defaults-file=~/password.cnf -u ' . $_ENV['DB_USERNAME'] . ' -h ' . $_ENV['DB_HOST'] . ' ' . $_ENV['DB_DATABASE'] . ' < ~/caas.sql');
});
before('ultros:db', 'upload:db');

desc('Update the assets on Ultros');
task('ultros:assets', function() {
	upload('../assets/ffxiv/i/', '/srv/www/assets/i/');
});

desc('Upload all env files');
task('upload:envs', function() {
	// upload('.env.local', '/srv/.envs/.env.local');
	// upload('.env.dev', '/srv/.envs/.env.dev');
	upload('.env.qa', '/srv/.envs/.env.qa');
	upload('.env.production', '/srv/.envs/.env.production');
});

desc('Download all env files');
task('download:envs', function() {
	// download('/srv/.envs/.env.local', '.env.local');
	// download('/srv/.envs/.env.dev', '.env.dev');
	// download('/srv/.envs/.env.qa', '.env.qa');
	download('/srv/.envs/.env.production', '.env.production');
});

desc('Upload env file');
task('upload:env', function() {
	upload('.env.{{stage}}', '{{deploy_path}}/shared/.env');
});

desc('Download env file');
task('download:env', function() {
	download('{{deploy_path}}/shared/.env', '.env.{{stage}}');
});

desc('Execute artisan refresh');
task('artisan:refresh', function () {
	run('{{bin/php}} {{release_path}}/artisan refresh');
});

// Primary Task
desc('Deploy your project');
task('deploy', [
	'deploy:info',
	'deploy:prepare',
	'deploy:lock',
	'deploy:release',     // Prepare `release`
	'deploy:update_code', // GitHub call
	'deploy:shared',      // Create symlinks for shared_dirs and files
	'deploy:copy_dirs',   // Copies vendor/ folder
	'deploy:vendors',     // composer install --no-dev -o
	'deploy:writable',    // Makes sure storage folders are writable
	'artisan:refresh',    // artisan:optimize on steroids
	'artisan:migrate',    // Database migration
	'deploy:symlink',     // `release` is now `current`
	'deploy:unlock',
	'cleanup',
]);

// Additional task executions

// If the deployment fails, unlock it for future deployments
after('deploy:failed', 'deploy:unlock');

// Create a storage/app/public symlink
// after('deploy:shared', 'artisan:storage:link');
