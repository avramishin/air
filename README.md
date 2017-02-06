# Air PHP framefork
Directory structure

* /app - directory for your app, all development here
  * /assets - js, css, images and other stuff
  * /conf - json configuration files
    * config.json - default configuration
    * config.local.json - local configuration to override default values
  * /controllers - your controllers, files & dir structure is url components
  * /cron - your scripts used in crontab
  * /events - event's handlers
  * /libs - dir for thrirdparty libs if they can't be installed from composer
  * /models - dir for app models, logic and auto generated DB files  
  * /views - twig templates
  * bootstrap.php - app bootsrap file, put here anything you need to do before any actions
  * router.php - app router in case if you want override default file based routing 
* /core - framework core functions and classes, pls avoid changes here if you don't develop framework itself 
* /data - directory to store local data if any
  * /tmp - all temporary files 
  * /logs - all logs
* /tools - various cli tools 
  * /db-model-generator - run cli "php generate.php" to create/update models for database tables 
* /vendor - composer files, run "composer update" to create/update dependencies