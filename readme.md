# FFXIV Crafting
## Crafting As A Service
### An online tool to help crafters in Final Fantasy XIV: A Realm Reborn.

## Development

Setup:
Laravel 5 Homestead - http://laravel.com/docs/5.0/homestead

List of necessary downloads to Windows Machine:
- Vagrant
- VirtualBox
- NodeJS
- Ruby (Installation Note: Click "Add Ruby to PATH" option)
- GitHub

Install Ruby, and `gem install sass`.
I was met with an error here (Windows 8.1).  This thread had a workaround: http://stackoverflow.com/questions/27278966/error-sass-installation-for-windows, but to summarize, run `gem source -a http://rubygems.org/` then try to install sass again.

Install NodeJS and run `grunt install` in the project directory, followed by `grunt`.

ImageMagick needs installed inside Homestead/Vagrant.


The `ffxivcrafting` schema should be manually created.

---

Custom artisan commands:

- php artisan db:unpack
 - Requires 7Zip
  - `sudo apt-get install p7zip-full`
- php artisan db:build

---

## Routine Updates

Image for Footer: http://na.finalfantasyxiv.com/lodestone/special/patchnote_log/