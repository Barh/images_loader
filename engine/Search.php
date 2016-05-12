<?php

    use \Aside\Includes\Images as Images;

    ## Получаем список уникальных параметров
    if ( !isset($_POST['progress']) )
        $parameters = Images::getParameters();
    else
    {
        ## Создаем объект
        //$json = new stdClass();

        foreach ($_POST['data'] as $value)
        {
            $_POST[$value['name']] = $value['value'];
        }

        $parameters = Images::getParameters($_POST);

        //$json->result = true;

        ob_start();
        include 'progress_parameters_html.php';
        $html = ob_get_clean();

        echo $html;
        //$json->html = ob_get_clean();

        ## Выводим информацию в JSON-формате
        //echo json_encode($json);

        ## Завершаем выполнение скрипта
        exit;
    }

    ## Если пришел запрос на Поиск
    if ( isset($_POST['search']) && !empty($_POST['search']) )
    {
        ## Получаем изображения по поисковым параметрам
        $search_items = Images::searchByParameters( $_POST );
    }