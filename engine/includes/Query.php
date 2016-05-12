<?php

    namespace Aside\Includes;

    class Query
    {
        const INSERT = 'INSERT INTO';
        //const VALUES = 'VALUES';

        const UPDATE = 'UPDATE';
        const SET    = 'SET';

        const DELETE = 'DELETE FROM';

        const SELECT = 'SELECT';
        const FROM   = 'FROM';

        const WHERE  = 'WHERE';

        static function getColumns( $columns = false )
        {
            // *
            if ( empty($columns) )
                $columns = array('*');
            elseif ( !is_array($columns) )
                $columns = explode(',', $columns);

            $columns = array_map( 'trim', $columns );

            $columns_sql = '';
            foreach ($columns as $value)
                $columns_sql .= self::framing($value, '`').',';

            return self::cutString($columns_sql);
        }

        static function getCount( $string )
        {
            return 'COUNT('.$string.')';
        }

        /**
         * FRAMING
         * @static
         * @param $string
         * @param $start
         * @param bool $end
         * @return string
         */
        static function framing($string, $start, $end = false)
        {
            if ( is_array($string) )
                return $string[0];

            // Костыль toDo Удалить
            if (/*substr($string , 0, 1) === '`' && substr($string , -1) == '`' && strrpos($string, ',') === false || */$string === 'NOW()' || $string === '*')
                return $string;

            if ( is_int($string) )
               return (int)$string.$end;

            if ($end != false)
                return $start.$string.$end;
            else
                return $start.$string.$start;
        }

        static function framingKey( $string, $framing )
        {
            return $framing.$string.$framing;
        }

        static function cutString($string)
        {
            return substr($string , 0, -1);
        }

        static function addSpace($array)
        {
            $string = '';
            foreach ($array as $value)
            {
                $string .= $value.' ';
            }

            return self::cutString($string);
        }

        static function arrayToSelect($array)
        {
            return self::arrayToSet($array, true);
        }

        static function arrayToSet($array, $select = false)
        {
            $string = '';

            if ($select)
                $delimiter = ' AND ';
            else
                $delimiter = ',';

            foreach ($array as $key => $value)
            {
                if ( is_array($value) )
                {
                    $string .= self::framingKey($key, '`').' IN (';
                    foreach ($value as $val)
                    {
                        if ($val !== NULL)
                            $string .= self::framing($val, "'").',';
                        else
                            $string .= 'NULL'.',';
                    }
                    $string = self::cutString($string).')'.$delimiter;
                }
                else
                {
                    $string .= self::framingKey($key, '`').' = ';
                    if ($value !== NULL)
                        $string .= self::framing($value, "'").$delimiter;
                    else
                        $string .= 'NULL'.$delimiter;
                }
            }

            if ( substr($string, -1) == ',' )
                $string = self::cutString($string);
            else
                $string = substr($string , 0, -5);

            return $string;
        }

        static function arrayToString($array, $framing = false)
        {
            $string = array('key' => '', 'value' => '');

            foreach ($array as $key => $value)
            {
                $string['key'] .= self::framingKey($key, '`').',';
                if ($value !== NULL)
                    $string['value'] .= self::framing($value, "'").',';
                else
                    $string['value'] .= 'NULL'.',';
            }

            $string['key']    = self::cutString($string['key']);
            $string['value']  = self::cutString($string['value']);

            if ($framing != false)
            {
                $string['key']   = self::framing($string['key'], '(', ')');
                $string['value'] = self::framing($string['value'], '(', ')');
            }

            return $string;
        }
    }