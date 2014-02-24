% cd admin
% ls
README.md  app.php  composer.json  config.php  public/
% cd public
% ls
index.php
% mkdir assets
% mkdir assets/coffee
% mkdir assets/sass
% mkdir assets/vendor/
% mkdir assets/js
% mkdir assets/css
% cd assets
% compass create seaf --bare --syntax sass --sass-dir "sass" --css-dir "css" --javascripts-dir "js" --images-dir "img"
[32mdirectory[0m seaf/ 
[32mdirectory[0m seaf/sass/ 
[32m   create[0m seaf/config.rb 

*********************************************************************
Congratulations! Your compass project has been created.

You may now add sass stylesheets to the sass subdirectory of your project.

Sass files beginning with an underscore are called partials and won't be
compiled to CSS, but they can be imported into other sass stylesheets.

You can configure your project by editing the config.rb configuration file.

You must compile your sass stylesheets into CSS when they change.
This can be done in one of the following ways:
  1. To compile on demand:
     compass compile [path/to/project]
  2. To monitor your project for changes and automatically recompile:
     compass watch [path/to/project]

More Resources:
  * Website: http://compass-style.org/
  * Sass: http://sass-lang.com
  * Community: http://groups.google.com/group/compass-users/
% ls -la
合計 68
drwxr-xr-x 8 kurari project 8  2月 25 07:58 ./
drwxr-xr-x 3 kurari project 5  2月 25 07:55 ../
drwxr-xr-x 2 kurari project 2  2月 25 07:55 coffee/
drwxr-xr-x 2 kurari project 2  2月 25 07:56 css/
drwxr-xr-x 2 kurari project 2  2月 25 07:56 js/
drwxr-xr-x 2 kurari project 2  2月 25 07:56 sass/
drwxr-xr-x 3 kurari project 4  2月 25 07:58 seaf/
drwxr-xr-x 2 kurari project 2  2月 25 07:56 vendor/
% 
