$(function() {

    ImagesPreset = function()
    {
        var _self = this;

        _self.body               = 'body';
        _self.request_process_id = 'request-process';
        _self.request_process    = '#' + _self.request_process_id;

        this.init = function ()
        {
            _self.uploadTypeSelector();
            _self.spoiler();
            _self.delete();
            _self.progress();
        };

        this.uploadTypeSelector = function ()
        {
            var selector = '.upload-type-selector > input';

            $(selector).click( function() {
                var type = $(this).val();

                if (type == 'local')
                {
                    $( '.upload-url').hide();
                    $( '.upload-local').show();
                }
                else
                {
                    $( '.upload-local').hide();
                    $( '.upload-url').show();
                }
            } );
        };

        this.spoiler = function ()
        {
            var spoiler = '.spoiler';

            $(spoiler).click( function() {
                var type = $(this).val();

                if (type == 'РАЗВЕРНУТЬ')
                {
                    $(this).parent().parent().find('.image-data-container').show();
                    $(this).val('СВЕРНУТЬ');
                }
                else
                {
                    $(this).parent().parent().find('.image-data-container').hide();
                    $(this).val('РАЗВЕРНУТЬ');
                }

                return false;
            } );
        };

        this.delete = function ()
        {
            var spoiler = '.delete';

            $(spoiler).click( function() {
                name = $(this).prop('name');

                $.ajax({
                    url: document.location.href,
                    type: "POST",
                    dataType: 'json',
                    data: { "id" : name, "delete" : "y"} ,
                    success: function(data)
                    {
                        if (data.result)
                        {
                            $('.image-' + name).remove();
                        }
                    }
                });

                if (type == 'РАЗВЕРНУТЬ')
                {
                    $(this).parent().parent().find('.image-data-container').show();
                    $(this).val('СВЕРНУТЬ');
                }
                else
                {
                    $(this).parent().parent().find('.image-data-container').hide();
                    $(this).val('РАЗВЕРНУТЬ');
                }
            } );
        };

        this.progress = function ()
        {
            $(document).on('change', 'table.search-container select', function () {
                var search = $('#form-for-progress').serializeArray();

                $.ajax({
                    url: document.location.href,
                    type: "POST",
                    dataType: 'html',
                    data: { "progress" : true, "data" : search} ,
                    success: function(data)
                    {
                        var  table = $(data).filter('table');

                        $('#form-for-progress table').replaceWith( table );
                    }
                });
            });
        };

    };



    document.ImagesPreset = new ImagesPreset();
    document.ImagesPreset.init();

});