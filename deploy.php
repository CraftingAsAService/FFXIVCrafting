<?php

namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'ffxivcrafting');
set('allow_anonymous_stats', true);

// Project repository
set('repository', 'git@github.com:CraftingAsAService/FFXIVCrafting.git');

set('git_tty', true);
set('git_cache', false);
// set('git_clone_dissociate', false); // TESTING

// Preserve shared/writable/copy dirs & files from the
//  laravel recipe with array_merge
set('shared_dirs', array_merge(get('shared_dirs'), [
	'public/assets',
]));

// set('shared_files', array_merge(get('shared_files'), [
// ]));

// set('writable_dirs', array_merge(get('writable_dirs'), [
// ]));

set('copy_dirs', array_merge(get('copy_dirs'), [
	'vendor'
]));

// Hosts

host('ultros')
	->user('$DOUSER') // Server user setup in ~/.aliases
	->forwardAgent() // Use local ssh credentials for git
	->stage('production')
	->set('http_user', 'www-data')
    ->set('deploy_path', '/srv/test/{{application}}');

// Tasks

desc('ENV File Setup');
task('environment', function() {
	run('rsync -v -e ssh .env.{{stage}} $(echo $DOUSER)@{{hostname}}:{{deploy_path}}/shared/.env');
});

desc('Execute artisan refresh');
task('artisan:refresh', function () {
    run('{{bin/php}} {{release_path}}/artisan refresh');
});

// Before deploying vendors, copy the vendor folder over
before('deploy:vendors', 'deploy:copy_dirs');

after('deploy:failed', 'deploy:unlock');
before('deploy:symlink', 'artisan:migrate');

before('artisan:optimize', 'environment');
before('artisan:optimize', 'artisan:refresh');
