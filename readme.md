# FFXIV Crafting
## Crafting As A Service
### An online tool to help crafters in Final Fantasy XIV: A Realm Reborn.

## Thank you:

- https://xivapi.com
- https://github.com/xivapi/classjob-icons

## Routine Updates

Image for Footer: http://na.finalfantasyxiv.com/lodestone/special/patchnote_log/

## Updating

These commands should be done in Vagrant.

```
php artisan cache:clear file (optionally necessary)
php artisan aspir:data
php artisan aspir:migrate
php artisan aspir:build-db
php artisan aspir:assets
```

These commands should be done on the Mac.

```
dep cactuar:assets
dep artisan:down
dep cactuar:db
dep deploy
dep artisan:up
```

For the time being I also need to manually move the assets folder symlink

```
cd releases/##/public
mv ../../##/public/assets .
```

And while on the server, clear the view cache

```
php artisan view:clear
```
