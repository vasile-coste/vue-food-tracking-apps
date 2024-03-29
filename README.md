# This project is a MVP - Minimal Viable Product

### This project was build using Laravel v8 and VueJS v2 and consist of 4 apps:
- API (build using Laravel)
- Farmer App
- Packaging App
- Customer App

![preview](./preview.png)

------

# Laravel (v8)
## Prepare env
- `composer create-project laravel/laravel <appName>` or `laravel new <appName>` - create application
- `php artisan serve` - run app

## Goodies
- `php artisan migrate` run all migration files
- `php artisan make:migration <tableName>` - then update the file from database/migrations to add columns
- `php artisan make:controller <controllerName>` - create controller
- `php artisan make:model <modelName>` create a model, adding `--migration` -  or `-m` it will also create migration table

# VueJs (v2)
## Prepare env
- `npm install -g @vue/cli` install cli to simplify things

## Commands used to create the projects in git-cli:
- `vue create <appName>` create a vue app then follow the steps

## Run app in dev mode
- `npm run serve` compile and run app

## Run app in production mode
- `npm run build` compile and minify 


## Goodies (and used libraries)
- `npm i axios` similar to jquery ajax
- `npm i vue-router`
- `npm i vue-session` add session to project
- `npm i vuex`
- `npm i vue-notification` notification system
- `npm i bootstrap` add boostrap to project
- `npm i jquery`
- `npm i popper.js`
- `npm i leaflet`
- `npm i qrcode`
- `vue add pwa` - add PWA to a created app
- tut -> https://bitbucket.org/blog/creating-a-vue-js-application
