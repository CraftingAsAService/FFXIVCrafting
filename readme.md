# FFXIV Crafting
## Crafting As A Service
### An online tool to help crafters in Final Fantasy XIV: A Realm Reborn.

## Routine Updates

Image for Footer: http://na.finalfantasyxiv.com/lodestone/special/patchnote_log/

## Updating

```
php artisan cache:clear file (optionally necessary)
php artisan aspir:data
php artisan aspir:migrate

php artisan build
dep ultros:db

php artisan aspir:assets
dep ultros:assets

dep deploy
```
