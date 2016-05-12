<?php

    namespace Aside;

    /**
     * SETTINGS
     */
    class Settings
    {
        ## Устанавливает физический путь до корневой директории скрипта
        static $scriptPath, $server, $engineDir, $engineUrl, $tables, $language, $ajax, $ajaxFullHtml, $imagesDir;

        # DB CONNECT
        const DB_SERVER             = 'localhost';
        const DB_NAME               = 'see';
        const DB_USERNAME           = 'see';
        const DB_PASSWORD           = '';

        ## DB TABLES PREFIX
        const DB_TABLE_PREFIX       = 'il_';

        ## DB LOG
        //const DB_LOG                = '/log/mysql.log';

        ## DB DIFFERENCE TIME
        ## Если необходимо, находим разницу между временем Сервера и БД
        const DB_DIFFERENCE_TIME    = true;

        ## Debugger
        const DEBUGGER              = false; # ( true or false ) toDo В реально работающем приложении, отключить

        public static function init()
        {
            ## Устанавливает физический путь до корневой директории скрипта
            self::$scriptPath = __DIR__;

            ## Путь до директории с изображениями
            self::$imagesDir = __DIR__.'/../www/images/';

            ## The protocol and server name to use in fully-qualified URLs
            ## Протокол и имя сервера для обеспечения полностью верных URL
            self::$server = 'http://'. $_SERVER['HTTP_HOST'];

            ## Директория Aside Engine
            self::$engineDir = basename ( __DIR__ );

            ## URL-директория Aside Engine
            self::$engineUrl = self::$server.'/'.self::$engineDir;

            ## DB TABLE NAMES
            self::$tables = array(
                ## IMAGES
                'images',
            );

            self::$language = 'ru';

            if ( isset($_SERVER['HTTP_X_ASIDE_AJAX']) && $_SERVER['HTTP_X_ASIDE_AJAX'] == true )
                self::$ajax = true;
            else
                self::$ajax = false;

            if ( isset($_POST['full_html']) )
                self::$ajaxFullHtml = true;
            else
                self::$ajaxFullHtml = false;
        }

        /**
         * GET TABLE
         * @param $name
         * @return bool|string
         */
        public static function getTable($name)
        {
            if ( in_array($name, self::$tables) )
                return self::DB_TABLE_PREFIX.$name;

            return FALSE;
        }

        /**
         * SET LANGUAGE
         */
        public static function setLanguage()
        {
            self::$language = \Aside\Includes\Language::get( self::$language );
        }
    }

    ## SETTINGS INIT
    Settings::init();

    ## AutoLoader Classes
    ## АвтоЗагрузчик Классов
    include_once Settings::$scriptPath.'/includes/AutoLoader.php';