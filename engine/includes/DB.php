<?php

    namespace Aside\Includes;

    use \Aside\Settings as Settings;
    use \mysqli as mysqli;

    /**
     * DB
     * Работа с Базой Данных
     */
    class DB
    {
        ## Переменная для соединения с БД
        static $mysqli, $connect_error, $lang;

        ## Вычисляемая разница между временем Сервера и БД
        static $differenceTime = array(
            'sec' => 0
        );

        static $logError = false;

        /**
         * INIT
         */
        public static function init()
        {
            ## Подключение массива с Языковыми значениями
            self::$lang = Language::includeLanguage( __CLASS__ );
        }

        /**
         * CONNECT TO DB
         */
        private static function connect()
        {
            ## Создаем подключение с Базой Данных
            self::$mysqli = new mysqli ( Settings::DB_SERVER, Settings::DB_USERNAME, Settings::DB_PASSWORD, Settings::DB_NAME);

            ## Check connection
            ## Если есть ошибки в соединении с БД
            if ( self::$mysqli->connect_errno )
            {

                printf("Connect failed: %s\n", self::$connect_error);
                /*
                if (! Constructor::isRun() )
                {
                    Constructor::startTemplate( 'kk' );
                }
                Constructor::create('header', 'default');
                */
                exit();
            }

            ## Если не удалось выставить кодировку символов UTF-8
            if ( !self::$mysqli->set_charset("utf8") )
            {
                printf("Error loading character set utf8: %s\n", self::$mysqli->error);
                printf("Current character set: %s\n", self::$mysqli->character_set_name());
                exit();
            }

            ## Если необходимо, находим разницу между временем Сервера и БД

            if ( Settings::DB_DIFFERENCE_TIME )
            {
                self::differenceTime();
                // self::$differenceTime = $time['difference_sec'];
            }
        }

        /**
         * DIFFERENCE TIME BETWEEN SERVER & DB
         * Разница во времени между Сервером и БД
         * @return mixed
         */
        public static function differenceTime( $full = false )
        {
            if ( !self::$mysqli )
                self::connect();

            ## Если необходимо, находим разницу между временем Сервера и БД
            if ( Settings::DB_DIFFERENCE_TIME && !isset(self::$differenceTime['db']) )
            {
                ## Текущее время в БД
                $result = self::query('SELECT NOW() as `mysql_time`');
                if ($result->num_rows > 0)
                {
                    $array = self::resultToArray($result);
                    self::$differenceTime['db'] = $array[0]['mysql_time'];
                    unset($array);
                }

                ## Текущее время на Сервере
                self::$differenceTime['php'] = date( 'Y-m-d H:i:s', time() );

                ## Разница в секундах
                self::$differenceTime['sec'] = strtotime(self::$differenceTime['db']) - strtotime(self::$differenceTime['php']);

                ## Разница в минутах и секундах
                self::$differenceTime['min'] = intval(self::$differenceTime['sec'] / 60).'min '.(self::$differenceTime['sec'] - ( intval(self::$differenceTime['sec'] / 60) * 60 ) ).'sec';
            }

            return (!$full) ? self::$differenceTime['sec'] : self::$differenceTime;
        }

        /**
         * RESULT TO ARRAY
         * @param $result
         * @return array|bool
         */
        public static function resultToArray( $result, $numeric = false )
        {
            if (!$numeric)
                while( $row = $result->fetch_array(MYSQLI_ASSOC) )
                    $rows[] = $row;
            else
                while( $row = $result->fetch_array(MYSQLI_NUM) )
                    $rows[] = $row;

            return !empty($rows) ? $rows : FALSE;
        }

        /**
         * QUERY in DB
         * Запрос в БД
         * @static
         * @param $sql_query
         * @return array|bool
         */
        static function query( $query )
        {
            if ( !empty($query) )
            {
                ## Соединяемся с БД, если необходимо
                if ( !self::$mysqli )
                    self::connect();

                ## Делаем запрос
                $result = self::$mysqli->query( $query );

                ## Если запрос завершен ошибкой, то записываем в лог
                if ($result === false)
                    self::logError();
            }

            ## Возвращаем result
            return isset ($result) ? $result : false;
        }

        /**
         * Query Check Args
         * @param $args - массив аргументов
         * @param $type - тип запроса = select | insert | update | delete
         * @return bool
         */
        static function queryCheckArgs( $args, $type )
        {
            ## Количество аргументов
            $args_num = count($args);

            ## Тип
            switch($type)
            {
                /*case 'count':
                    switch ($args_num)
                    {
                        case 2:

                            ## Узнаем таблицу
                            $array['table']   = Variables::getClassVar( Variables::getCalledClass(), 'table' );

                            break;
                    }
                break;*/
                ## SELECT
                case 'select':
                case 'count':
                    switch ($args_num)
                    {
                        ## 2 Аргумента
                        case 2:
                            ## Узнаем таблицу
                            $array['table']   = Variables::getClassVar( Variables::getCalledClass(), 'table' );

                            if ($array['table'])
                            {
                                ## Что необходимо
                                $array['columns'] = $args[0];
                                ## Условие
                                $array['where']   = $args[1];
                            }
                            else
                            {
                                ## Таблица
                                $array['table']  = $args[0];
                                ## Что необходимо
                                $array['columns'] = $args[1];
                                ## Условие
                                $array['where']   = '';
                            }
                            break;

                        ## 3 Аргумента
                        case 3:
                            ## Таблица
                            $array['table']  = $args[0];
                            ## Что необходимо
                            $array['columns'] = $args[1];
                            ## Условие
                            $array['where'] = $args[2];
                            break;

                        ## 4 Аргумента
                        case 4:
                            ## Таблица
                            $array['table']  = $args[0];
                            ## Что необходимо
                            $array['columns'] = $args[1];
                            ## Условие
                            $array['where'] = $args[2];
                            ## LIMIT
                            $array['limit'] = $args[3];
                            break;


                        ## Нет аргументов
                        default:
                            return FALSE;
                            break;
                    }
                break;


                ## INSERT
                case 'insert':
                    switch ($args_num)
                    {
                        ## 1 Аргумент
                        case 1:
                            ## Узнаем таблицу
                            $array['table'] = Variables::getClassVar( Variables::getCalledClass(), 'table' );
                            ## Что вставить
                            $array['data'] = $args[0];
                            break;

                        ## 2 Аргумента
                        case 2:
                            ## Что вставить
                            $array['table'] = $args[0];
                            ## Что вставить
                            $array['data']  = $args[1];
                            break;

                        ## Нет аргументов
                        default:
                            return FALSE;
                            break;
                    }
                break;

                ## UPDATE
                case 'update':
                    switch ($args_num)
                    {
                        ## 2 Аргумента
                        case 2:
                            ## Узнаем таблицу
                            $array['table'] = Variables::getClassVar( Variables::getCalledClass(), 'table' );
                            ## Что вставить
                            $array['data'] = $args[0];
                            ## Условие
                            $array['where'] = $args[1];
                            break;

                        ## 3 Аргумента
                        case 3:
                            ## Таблица
                            $array['table'] = $args[0];
                            ## Что вставить
                            $array['data']  = $args[1];
                            ## Условие
                            $array['where'] = $args[2];
                            break;

                        ## Нет аргументов
                        default:
                            return FALSE;
                            break;
                    }
                break;

                ## DELETE
                case 'delete':
                    switch ($args_num)
                    {
                        ## 1 Аргумент
                        case 1:
                            ## Узнаем таблицу
                            $array['table'] = Variables::getClassVar( Variables::getCalledClass(), 'table' );
                            ## Условие
                            $array['where'] = $args[0];
                            break;

                        ## 2 Аргумента
                        case 2:
                            ## Таблица
                            $array['table'] = $args[0];
                            ## Условие
                            $array['where'] = $args[1];
                        break;

                        ## Нет аргументов
                        default:
                            return FALSE;
                            break;
                    }
                break;

                ## Не найден тип
                default:
                    return FALSE;
                break;
            }

            return $array;
        }

        /**
         * QUERY CREATE STRING AND RUN
         * @param $array
         * @param $type
         * @return array|bool
         */
        static function queryCreateString( $array, $type )
        {
            ## Если существует переменная с названием Таблицы
            if ( !empty($array['table']) )
            {
                ## Обрамляем название таблицы в косые кавычки
                $table = Query::framing( $array['table'] , '`');

                ## Если тип Select, Count
                if ( in_array($type, array('select', 'count')) )
                {
                    ## Если массив - создаем строку Условие для SQL-запроса
                    if ( is_array( $array['where'] ) )
                        $where = Query::arrayToSelect( $array['where'] );
                    ## Уже созданная строка
                    else
                        $where = $array['where'];
                }

                ## Если тип Update, Delete
                if ( in_array($type, array('update', 'delete') ) )
                {
                    ## Если массив - создаем строку Условие для SQL-запроса
                    if ( is_array( $array['where'] ) )
                        $where = Query::arrayToSet( $array['where'] );
                    ## Уже созданная строка
                    else
                        $where = $array['where'];

                    ## Если нет условий, то возвращаем FALSE
                    if ( empty($where) )
                        return FALSE;
                }

                ## Если тип Insert, Update
                if ( in_array($type, array('insert', 'update') ) )
                {
                    ## Создаем data-строку для SQL-запроса
                    if ( is_array( $array['data'] ) )
                        $data = Query::arrayToSet( $array['data'] );
                    ## Уже созданная строка
                    else
                        $data = $array['data'];

                    ## Если нет обязательной data-строки, то возвращаем FALSE
                    if ( empty($data) )
                        return FALSE;
                }

                ## Тип
                switch ($type)
                {
                    ## SELECT
                    case 'select':
                    case 'count':
                        if ($type == 'select')
                        {
                            ## Получаем строку - какие столбцы выбрать (COLUMNS)
                            $columns = Query::getColumns( $array['columns'] );
                        }
                        else
                        {
                            if (!is_array($array['columns']))
                                ## Получаем строку - какие столбцы выбрать (COLUMNS)
                                $columns = Query::getCount( $array['columns'] );
                            else
                                return FALSE;
                        }

                        $limit = !empty($array['limit']) ? $array['limit'] : '';

                        ## Формируем строку
                        if ( !empty($where) )
                            $sql = Query::addSpace( array(Query::SELECT, $columns, Query::FROM, $table, Query::WHERE, $where, $limit) );
                        else
                            $sql = Query::addSpace( array(Query::SELECT, $columns, Query::FROM, $table, $limit) );
                    break;

                    ## INSERT
                    case 'insert':
                        ## Формируем строку
                        $sql = Query::addSpace( array(Query::INSERT, $table, Query::SET, $data ) );
                    break;

                    ## UPDATE
                    case 'update':
                        ## Формируем строку
                        $sql = Query::addSpace( array(Query::UPDATE, $table, Query::SET, $data, Query::WHERE, $where) );
                    break;


                    ## DELETE
                    case 'delete':
                        ## Формируем строку
                        $sql = Query::addSpace( array(Query::DELETE, $table, Query::WHERE, $where) );
                    break;

                    ## Не указан правильный тип
                    default:
                        return FALSE;
                    break;
                }

                return self::query($sql);
            }

            return FALSE;
        }

        /**
         * QUERY SELECT
         * @static
         * @param $table
         * @param bool $columns
         * @param bool $where
         * @return array|bool
         */
        static function querySelect()
        {
            ## Проверяем Аргументы
            if ( !$array = self::queryCheckArgs( func_get_args(), 'select' ) )
                return FALSE;

            ## Составляем строку и делаем запрос
            return self::queryCreateString( $array, 'select');
        }

        /**
         * Query Insert
         * @param $table
         * @param $data
         * @return array|bool
         */
        static function queryInsert()
        {
            ## Проверяем Аргументы
            if ( !$array = self::queryCheckArgs( func_get_args(), 'insert' ) )
                return FALSE;

            ## Составляем строку и делаем запрос
            return self::queryCreateString( $array, 'insert');
        }

        /**
         * Query Update
         * @param $table
         * @param $data
         * @param $where
         * @return array|bool
         */
        static function queryUpdate()
        {
            ## Проверяем Аргументы
            if ( !$array = self::queryCheckArgs( func_get_args(), 'update' ) )
                return FALSE;

            ## Составляем строку и делаем запрос
            return self::queryCreateString( $array, 'update');
        }


        /**
         * Query Delete
         * @param $table
         * @param $where
         * @return array|bool
         */
        static function queryDelete()
        {
            ## Проверяем Аргументы
            if ( !$array = self::queryCheckArgs( func_get_args(), 'delete' ) )
                return FALSE;

            ## Составляем строку и делаем запрос
            return self::queryCreateString( $array, 'delete');
        }

        static function queryCount()
        {
            ## Проверяем Аргументы
            if ( !$array = self::queryCheckArgs( func_get_args(), 'count' ) )
                return FALSE;

            ## Составляем строку и делаем запрос
            $result = self::queryCreateString( $array, 'count');

            if ($result)
            {
                ## COUNT KEY
                $array = DB::resultToArray($result, true);

                ## COUNT
                if ($array[0][0] > 0)
                    return (int)$array[0][0];
            }

            ## NOT
            return FALSE;
        }

        /**
         * LOG or VIEW DISPLAY - ERROR
         * Создание log'a ошибки или вывод сообщения
         * @static
         */
        static function logError()
        {
            $error =  self::$mysqli->error;
            $trace =  debug_backtrace();

            $head = $error ? '<strong style="color:red">MySQL error: </b><br><b style="color:green">'. $error .'</strong><br><br>' : NULL;

            $error_log = date("Y-m-d h:i:s") .' '. $head .'
            <b>Query: </b><br>
            <pre><span style="color:#CC0000">'. $trace[1]['args'][0] .'</pre></span><br><br>
            <b>File: </b><b style="color:#660099">'. $trace[1]['file'] .'</b><br>
            <b>Line: </b><b style="color:#660099">'. $trace[1]['line'] .'</b>';

            if ( self::$logError )
                file_put_contents ( Settings::$scriptPath.Settings::DB_LOG , strip_tags($error_log) ."\n\n", FILE_APPEND | LOCK_EX);
            else
                die( $error_log );
        }

        static function real_escape_string( $var )
        {
            if ( !self::$mysqli )
                self::connect();

            return self::$mysqli->real_escape_string( $var );
        }
    }