<?php


    use \Aside\Includes\Images as Images;

    ## Если пришел запрос на загрузку файла локально или по URL
    if ( isset($_POST) && isset($_POST['upload']) && isset($_POST['type']) )
    {
        ## LOCAL
        if ( $_POST['type'] == 'local' )
        {
            ## Загружаем файл локально
            $parameters_upload = Images::uploadLocal();
        }
        ## URL
        elseif ( $_POST['type'] == 'url' )
        {
            ## Загружаем файл по URL
            $parameters_upload = Images::uploadUrl( $_POST['url'] );
        }

        ## Ошибка загрузки
        if (empty($parameters_upload))
            $error_upload = 'Изображение не загружено. Попробуйте еще раз.';

    }