<?php

    namespace Aside\Includes;

    use \Aside\Settings as Settings;

    class Images
    {
        static $table;

        const NUMBER_RESULT_FOR_COLOR = 5;

        ## INIT
        public static function init()
        {
            ## Таблицы
            self::$table = Settings::getTable('images');
        }

        /**
         * Загрузка изображения с компьютера
         * @return array|bool
         */
        public static function uploadLocal()
        {
            ## Если не пуст массив FILES
            if ( !empty($_FILES) )
            {
                ## Перебираем массив FILES
                foreach ($_FILES as $value)
                {
                    /*
                        UPLOAD_ERR_OK: 0
                        UPLOAD_ERR_INI_SIZE: 1
                        UPLOAD_ERR_FORM_SIZE: 2
                        UPLOAD_ERR_NO_TMP_DIR: 6
                        UPLOAD_ERR_CANT_WRITE: 7
                        UPLOAD_ERR_EXTENSION: 8
                        UPLOAD_ERR_PARTIAL: 3
                    */

                    ## Если файл передан
                    if ($value['error'] === 0)
                    {
                        ## Путь для сохранения файла (временный)
                        $pathToSave = Settings::$imagesDir.md5( time() );

                        ## Если файл успешно перенесён в необходимую директорию
                        if ( move_uploaded_file($value['tmp_name'], $pathToSave ))
                        {
                            return self::upload( $pathToSave, $value['name'] );
                        }
                    }
                }
            }

            return FALSE;
        }

        /**
         * Загрузка изображения по URL
         * @return array|bool
         */
        public static function uploadURL( $urlToSave )
        {
            ## Запрашиваем заголовки
            @$h = get_headers($urlToSave, 1);

            ## Если есть Заголовок и его код равен 200 (всё корректно)
            if ( $h && strstr($h[0], '200') !== FALSE )
            {
                ## Копируем файл
                ## Путь для сохранения файла (временный)
                $pathToSave = Settings::$imagesDir.md5( time() );

                ## Если файл успешно скопирован
                if ( copy( $urlToSave, $pathToSave ) )
                {
                    return self::upload( $pathToSave, basename($urlToSave) );
                }
            }

            return FALSE;
        }

        /**
         * Загрузка изображения + Добавление в БД
         * @param $pathToSave
         * @param $name
         * @return array|bool
         */
        public static function upload( $pathToSave, $name )
        {
            ## Обрабатываем имя файла
            if ( ( $pos = strrpos($name, '.') ) !== false)
                $name = substr($name, 0, $pos);

            $name = trim($name);

            ## Если файл картинка
            if ( $data = self::getImageInfo( $pathToSave ) )
            {
                ## Запускаем класс для получения преобладающих цветов картинки
                $img = new GeneratorImageColorPalette;
                $colors = $img->getImageColor( $pathToSave, 2, 10 );

                $to_db = array(
                    'name'      => $name,
                    'size'      => (int)$data['size'],
                    'width'     => (int)$data['width'],
                    'height'    => (int)$data['height'],
                    'extension' => $data['extension'],
                    'mime'      => $data['mime'],
                    'color_1'   => isset($colors[0]) ? strtolower($colors[0]) : '',
                    'color_2'   => isset($colors[1]) ? strtolower($colors[1]) : '',
                    'created'   => 'NOW()',
                );

                ## Записываем данные в БД
                DB::queryInsert(
                    self::$table,
                    $to_db
                );
                ## Узнаём ID файла
                $file_id = DB::$mysqli->insert_id;

                ## Переименовываем картинку по ID, что бы было корректное Расширение
                rename($pathToSave, Settings::$imagesDir.$file_id.'.'.$data['extension']);

                return array_merge( $to_db, array( 'id' => $file_id, 'file' => $file_id.'.'.$data['extension']) );
            }
            else
            {
                unlink($pathToSave);
            }

            return FALSE;
        }

        /**
         * Получение информации о изображении
         * @param null $file
         * @return array|bool
         */
        private static function getImageInfo ($file = NULL)
        {
            if( !is_file($file) ) return false;

            if ( !$data = getimagesize($file) or !$filesize = filesize($file) )
                return false;

            if ( !in_array( $data[2], array(1,2,3) ) )
                return FALSE;

            $extensions = array(
                1 => 'gif', 2 => 'jpg',
                3 => 'png', 4 => 'swf',
                5 => 'psd', 6 => 'bmp',
                7 => 'tiff', 8 => 'tiff',
                9 => 'jpc', 10 => 'jp2',
                11 => 'jpx', 12 => 'jb2',
                13 => 'swc', 14 => 'iff',
                15 => 'wbmp', 16 => 'xbmp');

            $result =
                array(
                    'width'     => $data[0],
                    'height'    => $data[1],
                    'extension' => $extensions[$data[2]],
                    'size'      => $filesize,
                    'mime'      => $data['mime'],
                );

            return $result;
        }

        /**
         * Получение всех параметров
         * @return bool
         */
        public static function getParameters( $filter = false )
        {
            $sql_string = '';

            if ($filter)
            {
                if ( !empty($filter['size']) )
                    $sql_string .= " AND `size` = '".DB::real_escape_string($filter['size'])."' ";
                if ( !empty($filter['width']) )
                    $sql_string .= " AND `width` = '".DB::real_escape_string($filter['width'])."' ";
                if ( !empty($filter['height']) )
                    $sql_string .= " AND `height` = '".DB::real_escape_string($filter['height'])."' ";
                if ( !empty($filter['extension']) )
                    $sql_string .= " AND `extension` = '".DB::real_escape_string($filter['extension'])."' ";
            }

            $result = DB::querySelect(
                self::$table,
                array(
                    'size',
                    'color_1',
                    'color_2',
                    'width',
                    'height',
                    'extension',
                ),
                '`id` > 0'.$sql_string
            );

            ## SUCCESS
            if ( $result->num_rows > 0 )
            {
                $array = DB::resultToArray( $result );

                foreach ($array as $value)
                {
                    if ( !empty($value['width']) )
                        $width[]     = $value['width'];

                    if ( !empty($value['height']) )
                        $height[]    = $value['height'];

                    if ( !empty($value['extension']) )
                        $extension[] = $value['extension'];

                    if ( !empty($value['size']) )
                        $size[] = $value['size'];

                    if ( !empty($value['color_1']) )
                        $color_1[] = $value['color_1'];

                    if ( !empty($value['color_2']) )
                        $color_2[] = $value['color_2'];
                }

                if ( isset($width) )
                    $data['width'] = array_unique($width);
                if ( isset($height) )
                    $data['height'] = array_unique($height);
                if ( isset($extension) )
                    $data['extension'] = array_unique($extension);
                if ( isset($size) )
                    $data['size'] = array_unique($size);
                if ( isset($color_1) )
                    $data['color_1'] = array_unique($color_1);
                if ( isset($color_2) )
                    $data['color_2'] = array_unique($color_2);
            }

            return isset($data) ? $data : FALSE;
        }

        /**
         * Поиск по параметрам
         * @param $data
         */
        public static function searchByParameters( $data )
        {
            $sql_string = '';

            if ( !empty($data['width']) )
                $sql[] = "`width` = ".(int)$data['width'];

            if ( !empty($data['height']) )
                $sql[] = "`height` = ".(int)$data['height'];

            if ( !empty($data['extension']) )
                $sql[] = "`extension` = '".DB::real_escape_string($data['extension'])."'";

            if ( !empty($data['size']) )
                $sql[] = "`size` = ".(int)$data['size'];

            if ( !empty($data['color_1']) )
                $sql[] = "`color_1` = '".DB::real_escape_string($data['color_1'])."'";

            if ( !empty($data['color_2']) )
                $sql[] = "`color_2` = '".DB::real_escape_string($data['color_2'])."'";

            if ( isset($sql) && is_array($sql) )
            {
                foreach ($sql as $value)
                {
                    $sql_string .= $value.' AND ';
                }

                $sql_string = substr( $sql_string, 0, -4 );
            }
            else
                $sql_string = '';

            ## Выбираем параметры
            $result = DB::querySelect(
                self::$table,
                array(
                    'id',
                    'name',
                    'width',
                    'height',
                    'extension',
                    'size',
                    'color_1',
                    'color_2',
                ),
                $sql_string
            );

            ## SUCCESS
            if ($result->num_rows > 0)
            {
                $array = DB::resultToArray($result);

                ## Если задан параметр Преобладащий цвет 1 и он корректен
                if ( !empty($data['color_1']) && ( $rgb_color_1 = self::hex2RGB( $data['color_1'] ) ) )
                {
                    ## Перебираем изображения
                    foreach ($array as $key=>$value)
                    {
                        ## Если цвет корректен
                        if ( ( $rgb_color_image = self::hex2RGB( $value['color_1'] ) ) )
                        {
                            $sort_distance_1[$key] = $array[$key]['distance_1'] = self::calculateDistance($rgb_color_1, $rgb_color_image);
                        }
                        else
                            $sort_distance_1[$key] = $array[$key]['distance_1'] = false;
                    }

                    ## Сортируем по значению в убывающем порядке
                    array_multisort($sort_distance_1, SORT_NUMERIC, SORT_ASC, $array);
                    $array = array_slice($array, 0, self::NUMBER_RESULT_FOR_COLOR);
                }

                ## Если задан параметр Преобладащий цвет 2 и он корректен
                if ( !empty($data['color_2']) && ( $rgb_color_2 = self::hex2RGB( $data['color_2'] ) ) )
                {
                    ## Перебираем изображения
                    foreach ($array as $key=>$value)
                    {
                        ## Если цвет корректен
                        if ( ( $rgb_color_image = self::hex2RGB( $value['color_2'] ) ) )
                        {
                            $sort_distance_2[$key] = $array[$key]['distance_2'] = self::calculateDistance($rgb_color_2, $rgb_color_image);
                        }
                        else
                            $sort_distance_2[$key] = $array[$key]['distance_2'] = false;
                    }

                    ## Сортируем по значению в убывающем порядке
                    array_multisort($sort_distance_2, SORT_NUMERIC, SORT_ASC, $array);
                    $array = array_slice($array, 0, self::NUMBER_RESULT_FOR_COLOR);
                }

                return $array;
            }

            return FALSE;
        }

        /**
         * Convert a hexa decimal color code to its RGB equivalent
         *
         * @param string $hexStr (hexadecimal color value)
         * @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
         * @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
         * @return array or string (depending on second parameter. Returns False if invalid hex color value)
         */
        public static function hex2RGB( $hexStr, $returnAsString = false, $seperator = ',')
        {
            $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
            $rgbArray = array();
            if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
                $colorVal = hexdec($hexStr);
                $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
                $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
                $rgbArray['blue'] = 0xFF & $colorVal;
            } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
                $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
                $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
                $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
            } else {
                return false; //Invalid hex color code
            }
            return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
        }

        static public function calculateDistance( $rgb_1, $rgb_2 )
        {
            $red    = pow($rgb_1['red'] - $rgb_2['red'], 2);
            $green  = pow($rgb_1['green'] - $rgb_2['green'], 2);
            $blue   = pow($rgb_1['blue'] - $rgb_2['blue'], 2);

            return $distance = sqrt($red + $green + $blue);
        }

        /**
         * Печатаем параметры изображения (HTML)
         * @param $data
         */
        public static function printImageParameters( $data )
        {
            include Settings::$scriptPath.'/image_html.php';
        }

        /**
         * Удаляем изображение
         * @param $id
         * @return bool
         */
        public static function delete( $id )
        {
            $result = DB::querySelect(
                self::$table,
                array(
                    'extension'
                ),
                array(
                    'id' => (int)$id,
                )
            );

            ## SUCCESS
            if ($result->num_rows > 0)
            {
                $array = DB::resultToArray($result);

                ## DELETE
                DB::queryDelete(
                    self::$table,
                    array( 'id' => (int)$id )
                );

                unlink( Settings::$imagesDir.(int)$id.'.'.$array[0]['extension'] );

                return TRUE;
            }

            return FALSE;
        }

    }