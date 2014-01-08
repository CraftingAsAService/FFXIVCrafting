### Requirements:
* PHP 5.4.3+
* Composer
* MySQL
* Git Bash
* Review Laravel Documentation (http://laravel.com)

#### Instructions:
1. Fork Repo.
2. Clone Repo.
3. In your cloned repo folder, locate the `bootstrap/start.php` file. On line 31, you will need to add your computer name in for laravel to recognize the next commands.
4. Create the MySQL Database named `ffxiv-caas`.
5. Modify your apache config to point to the `public` folder.
6. In Git Bash, navigate to your Cloned Repo folder from Step-2. If you don't rename the folder, this will show as 'ffxiv-caas'.
7. Run `php composer.phar install` or `composer install` depending on how you installed composer.
8. Run: `php artisan migrate`
9. Run: `php artisan migrate:refresh --seed`
10. Run: `git checkout -b new-feature`
11. Begin Coding

#### To refresh your data:
1. `php artisan migrate:refresh --seed` again
2. `php artisan cache:clear`

Note: `NUL` works in Windows, and `/dev/null` works for Linux.  The ampersand ignores the output and just executes.

#### Forking Workflow References:
* https://www.openshift.com/wiki/github-workflow-for-submitting-pull-requests
* https://github.com/sevntu-checkstyle/sevntu.checkstyle/wiki/Development-workflow-with-Git%3A-Fork,-Branching,-Commits,-and-Pull-Request

