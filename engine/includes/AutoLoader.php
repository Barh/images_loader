<?php

    namespace Aside\Includes;

    class AutoLoader
    {
        public static function init()
        {
            // Включаем AutoLoader ссылаясь на метод
            spl_autoload_register( __CLASS__ .'::autoload' );
        }

        /**
         * AutoLoader Classes
         * @param $className
         */
        private static function autoload($classNameFull)
        {
            $classNameNormal = $classNameFull;

            # Задаем путь
            $classNameFull = str_replace('\\', '/', $classNameFull) . '.php';

            # Заменяем Aside на путь к скрипту, заданному в
            # LocalSettings.php
            $classNameFull = preg_replace('/^Aside/i', \Aside\Settings::$scriptPath, $classNameFull, 1);

            # Приводим путь в порядок, приводим к нижнему регистру
            # буквы, где это необходимо (в полном пути к файлу, кроме
            # названия класса.
            $className = substr ( $classNameFull, strrpos( $classNameFull, '/') + 1 );
            $classPath = strtolower( substr ( $classNameFull, 0, strrpos( $classNameFull, '/') + 1 ) );

            # Конечный путь до Класса
            $classPath = $classPath.$className;

            # Очищаем память Сервера
            unset ($className, $classNameFull);

            # Подключаем файл с классом, если такой найден
            if ( file_exists( $classPath ) )
                include_once( $classPath );

            # Запускаем метод init, если он существует
            if ( method_exists($classNameNormal, 'init' ) )
                $classNameNormal::init();
        }
    }

    AutoLoader::init();
