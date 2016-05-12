<?php

    use \Aside\Includes\Tables as Tables;
    use \Aside\Includes\Images as Images;

    ini_set("max_execution_time", "60");

    # ERRORS
    ini_set('display_errors', 'on');
    error_reporting(E_ALL); # Записывать(показывать) все ошибки

    # Подключаем Настройки
    chdir('../engine');
    include_once 'Settings.php';

    // Prepare tables
    if (!Tables::is()) {
        Tables::create();
    }

    ## Если пришел запрос на удаление и он корректен
    if ( isset($_POST['delete']) && $_POST['delete'] == 'y' && !empty($_POST['id']))
    {
        ## Создаем объект
        $json = new stdClass();

        ## Если удаление прошло успешно
        if ( Images::delete( $_POST['id'] ) )
        {
            $json->result = true;
        }
        ## Если удаление не выполнено
        else
        {
            $json->result = false;
        }
        ## Выводим информацию в JSON-формате
        echo json_encode($json);
        ## Завершаем выполнение скрипта
        exit;
    }

    ## Подключаем файл Upload.php
    include_once 'Upload.php';

    ## Подключаем файл Search.php
    include_once 'Search.php';

?>

<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="description" content="">
        <meta name="keywords" content="">
        <link href="/css/preset.css" type="text/css" rel="stylesheet">
        <script src="/js/jquery-1.9.1.min.js" type="text/javascript"></script>
        <script src="/js/jquery.preset.js" type="text/javascript"></script>
        <script src="/js/jquery.browser_detect.js" type="text/javascript"></script>
        <title>База текстурных изображений</title>
    </head>
    <body>
        <!-- MAIN CONTAINER -->
        <div>
            <!-- UPLOAD BG -->
            <div class="upload-bg">
                <!-- CONTAINER -->
                <div class="main-container">

                    <!-- UPLOAD IMAGE (FORM) -->
                    <form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <input type="hidden" name="upload">

                        <!-- Поле MAX_FILE_SIZE должно быть указано до поля загрузки файла -->
                        <input type="hidden" name="MAX_FILE_SIZE" value="5242880" />

                        <!-- TITLE -->
                        <div class="upload-title">Выберите вариант загрузки изображения:</div>

                        <!-- TYPE SELECTOR -->
                        <div class="upload-type-selector">
                            <!-- LOCAL FILE -->
                            <input name="type" type="radio" value="local" checked="checked" id="file_point" />
                            <label for="file_point" class="upload-local-file">С компьютера</label>
                            <!-- URL FILE -->
                            <input name="type" type="radio" value="url" id="url_point" />
                            <label for="url_point">URL</label>
                        </div>

                        <!-- AVAILABLE -->
                        <div class="available-list">
                            - формат: JPG, GIF или PNG<br/>
                            - размер: до 2 мегабайт
                        </div>

                        <!-- INPUT FILE or URL -->
                        <table>
                            <tr>
                                <td>
                                    <div class="upload-local-url-container">
                                        <!-- Название элемента input определяет имя в массиве $_FILES -->
                                        <input type="file" name="image" class="upload-local input-1" />
                                        <input type="text" name="url" placeholder="Введите URL-адрес изображения" class="upload-url input-1" />
                                    </div>
                                </td>
                                <td>
                                    <!-- SUBMIT -->
                                    <input type="submit" name="send" value="ЗАГРУЗИТЬ" class="upload-button button-1" />
                                </td>
                            </tr>
                        </table>
                    </form>

                    <?php
                    ## Если существует ошибка при загрузке изображения
                    if (isset($error_upload)): ?>
                    <div class="error-upload">
                        <span>
                            <?php echo $error_upload; ?>
                        </span>
                    </div>
                    <?php endif; ?>

                    <?php
                    ## Если изображение загружено корректно
                    if ( isset($parameters_upload) && !(isset($error_upload)) ) :?>
                        <?php Images::printImageParameters($parameters_upload); ?>
                    <?php endif; ?>

                </div>

            </div>


            <!-- SEPARATE LINE -->
            <div class="separate-bg"></div>

            <!-- SEARCH -->
            <div class="search-bg">

                <!-- CONTAINER -->
                <div class="main-container">

                <?php
                ## Если найдены текстуры
                if ( !empty($search_items) ): ?>

                <!-- SEPARATE LINE -->
                <div class="separate-bg-2"></div>

                <!-- RESULT CONTAINER -->
                <div class="search-result-bg">

                    <!-- SEARCH TITLE -->
                    <div class="search-title">Найдено <?php echo $search_count = count($search_items); ?> изображени<?php

                        ## Пишем нужное окончание
                        $search_last = substr($search_count, -1);

                        if ( in_array( $search_last, array(1) ) )
                            echo 'е';
                        elseif ( in_array( $search_last, array(2,3,4) ) )
                            echo 'я';
                        else
                            echo 'й';

                        ?></div>

                    <!-- RESULT -->
                    <div class="search-result">

                        <?php
                        ## Перебираем все найденные текстуры
                        foreach ($search_items as $key => $value): ?>
                            <!-- IMAGE BLOCK -->
                            <div class="image-block image-<?php echo $value['id']; ?>">
                                <div>
                                    <!-- IMAGE NAME -->
                                    <div class="image-name" title="<?php echo $value['name']; ?>">
                                        <?php echo $key+1; ?>. <?php echo $value['name']; ?>
                                    </div>
                                    <!-- BUTTONS -->
                                    <input type="submit" class="button-1 spoiler" value="РАЗВЕРНУТЬ" name="<?php echo $value['id']; ?>" />
                                    <?php if (isset($_GET['admin']) && $_GET['admin'] == 'y'): ?>
                                    <input type="submit" class="button-1 delete" value="УДАЛИТЬ" name="<?php echo $value['id']; ?>" />
                                    <?php endif; ?>
                                    <div class="clear"></div>
                                </div>

                                <!-- IMAGE PARAMETERS -->
                                <div class="image-data-container" style="display: none;">
                                    <?php Images::printImageParameters($value); ?>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>

                <!-- SEPARATE LINE -->
                <div class="separate-bg-3"></div>

                <!-- SEARCH TITLE -->
                <div class="search-title">Вторичный отбор</div>

                <!-- FORM SEARCH -->
                <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" name="search">

                    <!-- TABLE -->
                    <table class="search-container">
                        <tr>
                            <td>
                                <!-- PARAMETERS CONTAINER -->
                                <div class="search-parameters-container">
                                    <div class="search-parameters-title">
                                        Характеристики изображения:
                                    </div>
                                    <table>
                                        <tr>
                                            <td></td>
                                            <td>
                                                <div style="width: 140px;"></div>
                                            </td>
                                        </tr>
                                        <?php if (!empty($parameters['width']) && !empty($parameters['height'])): ?>
                                        <!-- RESOLUTION -->
                                        <tr>
                                            <td>
                                                <label for="width">Разрешение (px):</label>
                                            </td>
                                            <td>
                                                <input type="hidden" value="<?php echo $_POST['width'] ?>" name="width" />
                                                <input type="hidden" value="<?php echo $_POST['height'] ?>" name="height" />

                                                <?php if (empty($_POST['width']) && empty($_POST['height'])): ?>
                                                <span>Любое</span>
                                                <?php else: ?>
                                                <span><?php echo empty($_POST['width']) ? '-' : $_POST['width']; ?></span>
                                                x
                                                <span><?php echo empty($_POST['height']) ? '-' : $_POST['height']; ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endif; ?>

                                        <?php if (!empty($parameters['extension']) ): ?>
                                        <!-- EXTENSION -->
                                        <tr>
                                            <td>
                                                <label for="extension">Формат:</label>
                                            </td>
                                            <td>
                                                <input type="hidden" value="<?php echo $_POST['extension'] ?>" name="extension" />
                                                <span><?php echo empty($_POST['extension']) ? 'Любой' : strtoupper($_POST['extension']); ?></span>
                                            </td>
                                        </tr>
                                        <?php endif; ?>

                                        <?php if (!empty($parameters['size']) ): ?>
                                        <!-- FILE SIZE -->
                                        <tr>
                                            <td>
                                                <label for="size">Размер файла (кбайт):</label>
                                            </td>
                                            <td>
                                                <input type="hidden" value="<?php echo $_POST['size'] ?>" name="size" />
                                                <span>
                                                    <?php echo empty($_POST['size']) ? 'Любой' : number_format( strtoupper($_POST['size']) / 1024 , 1, '.', ' '); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endif; ?>

                                        <?php if (!empty($parameters['color_1']) ): ?>
                                        <!-- COLOR 1 -->
                                        <tr>
                                            <td><label for="color_1">Преобладающий цвет 1:</label></td>
                                            <td>
                                                <input type="hidden" value="<?php echo $_POST['color_1'] ?>" name="color_1" />
                                                <span><?php echo (empty($_POST['color_1']) || !\Aside\Includes\Images::hex2RGB( $_POST['color_1'] ) ) ? 'Любой' : '#'.strtoupper($_POST['color_1']); ?></span>
                                            </td>
                                        </tr>
                                        <?php endif; ?>

                                        <?php if ( !empty($parameters['color_2']) ): ?>
                                        <!-- COLOR 2 -->
                                        <tr>
                                            <td><label for="color_2">Преобладающий цвет 2:</label></td>
                                            <td>
                                                <div class="prefix-color">#</div><input name="color_2" id="color_2" class="input-1 colors-input" maxlength="6" value="<?php echo (empty($_POST['color_2']) || !\Aside\Includes\Images::hex2RGB( $_POST['color_2'] ) ) ? '' : strtoupper($_POST['color_2']); ?>">
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </td>
                            <!-- SUBMIT -->
                            <td>
                                <input type="submit" name="search" value="ПОИСК" class="button-1 search-button">
                                <a href="/" class="button-1 search-button" style="display: block; margin-top: 10px; text-decoration: none; padding: 0; text-align: center; font-family: Arial; padding-top: 6px; height: 24px;">
                                    СБРОСИТЬ
                                </a>
                            </td>
                        </tr>
                    </table>

                </form>

                <?php elseif (isset($search_items)): ?>

                <!-- SEPARATE LINE -->
                <div class="separate-bg-2"></div>

                <!-- RESULT CONTAINER -->
                <div class="search-result-bg">
                    <!-- SEARCH TITLE -->
                    <div class="search-title">По заданным параметрам не найдено ни одного изображения.</div>
                </div>

                <!-- SEPARATE LINE -->
                <div class="separate-bg-3"></div>
                <?php endif; ?>

                <?php
                ## Если существуют параметры
                if (!empty($parameters) && empty($search_items) ):?>
                    <!-- SEARCH TITLE -->
                    <div class="search-title">Поиск изображения</div>

                    <!-- FORM SEARCH -->
                    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" name="search" id="form-for-progress">
                        <?php include_once 'progress_parameters_html.php' ?>
                    </form>
                </div>
                <?php
                ## Если нет параметров для поиска
                elseif ( empty($parameters) ): ?>
                <!-- SEARCH TITLE -->
                <div class="search-title">На данный момент не загружено ни одно изображение.</div>
                <?php endif; ?>


            </div>

        </div>
    </body>
</html>