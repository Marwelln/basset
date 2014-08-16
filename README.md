This is a forked and relaunched version of the closed down project jasonlewis/basset.

## Basset for Laravel 4

Basset is a better asset management package for the Laravel framework. Basset shares the same philosophy as Laravel. Development should be an enjoyable and fulfilling experience. When it comes to managing your assets it can become quite complex and a pain in the backside. These days developers are able to use a range of pre-processors such as Sass, Less, and CoffeeScript. Basset is able to handle the processing of these assets instead of relying on a number of individual tools.

- [Installation](https://github.com/Marwelln/basset/wiki/Installation)
- [Usage (how to use Basset)](https://github.com/Marwelln/basset/wiki/Usage-(how-to-use-Basset))


### LESS

If you are working with or want to work with LESS you need to add the `oyejorge/less.php` package to your `composer.json` file.

~~~
composer require oyejorge/less.php:@stable
~~~

You can then use it by adding `->apply('Less')` to your collection (see your published configuration file for more information).

~~~
...
$collection->add('path/to/file.less')->apply('Less');
...
~~~

### Minification (compression)

If you want to minify (compress) your assets (CSS/LESS/JS) you need to add two packages, `mrclay/minify` and `natxet/CssMin`.

~~~
composer require mrclay/minify:~2.2
composer require natxet/CssMin:~3.0
~~~

To use minification, you use `->apply('CssMin')` or `->apply('JsMin')`. Keep in mind though that minification will only run with the `production` environment. You will always have multiple files when working on your development environment for easier debugging.

~~~
php artisan basset:build --production
~~~

### Documentation

[View the official documentation](http://jasonlewis.me/code/basset/4.0).
