## FFXIV CAAS

### Requirements:
* PHP 5.4.3+
* Composer
* MySQL
* Git Bash

#### Instructions:
1. Fork Repo.
2. Clone Repo.
3. In your cloned repo folder, locate the `bootstrap/start.php` file. On line 31, you will need to add your computer name in for laravel to recognize the next commands.
4. Create the MySQL Database named `ffxiv-caas`.
5. In Git Bash, navigate to your Cloned Repo folder from Step-2. If you don't rename the folder, this will show as 'ffxiv-caas'.
6. Run `php composer.phar install` or `composer install` depending on how you installed composer.
7. Run: `php artisan migrate`
8. Run: `php artisan migrate:refresh --seed`
9. Run: `git checkout -b new-feature`
10. Begin playing... codez

#### Forking Workflow References:
* https://www.openshift.com/wiki/github-workflow-for-submitting-pull-requests
* https://github.com/sevntu-checkstyle/sevntu.checkstyle/wiki/Development-workflow-with-Git%3A-Fork,-Branching,-Commits,-and-Pull-Request




