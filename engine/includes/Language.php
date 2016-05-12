<?php

    namespace Aside\Includes;

    /**
     * LANGUAGE
     * Выбирает какой язык использовать
     */
    class Language
    {
        const RUSSIAN = 'ru';
        const ENGLISH = 'en';

        const DIRECTORY = '/languages/';

        public static function get( $var = false )
        {
            switch ($var)
            {
                case self::ENGLISH:
                    return self::ENGLISH;
                    break;
                case self::RUSSIAN:
                default:
                    return self::RUSSIAN;
                    break;
            }
        }

        static function includeLanguage( $classNameFull )
        {
            # Задаем путь
            $classNameFull = str_replace('\\', '/', $classNameFull) . '.php';

            # Заменяем Aside на путь к скрипту, заданному в
            # LocalSettings.php и добавляем путь к /languages/ru/
            $classNameFull = preg_replace(
                '/^Aside/i',
                \Aside\Settings::$scriptPath.self::DIRECTORY.\Aside\Settings::$language,
                $classNameFull,
                1);

            # Приводим путь в порядок, приводим к нижнему регистру
            # буквы, где это необходимо (в полном пути к файлу, кроме
            # названия класса(файла).
            $className = substr ( $classNameFull, strrpos( $classNameFull, '/') + 1 );
            $classPath = strtolower( substr ( $classNameFull, 0, strrpos( $classNameFull, '/') + 1 ) );

            # Конечный путь до файла
            $classPath = $classPath.$className;

            # Если есть языковой файл
            if ( file_exists( $classPath ) )
                include $classPath;

            # Если найден массив данных
            if ( isset ($lang) )
                return $lang;
            else
                return false;
        }

        static function includeLanguageBlock( $src )
        {
            # Если есть языковой файл
            if ( file_exists( $src ) )
                include $src;

            # Если найден массив данных
            if ( isset ($bLang) )
                return $bLang;
            else
                return false;
        }

    }