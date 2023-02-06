window.wp = window.wp || {};

wp.HYSTERYALE_UPDATER = (function(HYSTERYALE_UPDATER, $) {

    HYSTERYALE_UPDATER.Importer = {
        initialized: false,

        init: function() {
            if( ! this.initialized )
                this.bind();

            this.setConfigSelects();

            this.initialized = true;
            return this;
        },

        bind: function() {
            $('.js-importer-start').on( 'click', this.start);
            $('.hysteryale_updater__config_import').change(this.setConfigSelects);
        },

        setConfigSelects: function() {
            var $form = $('#hysteryale_updater__import_config');

            $form.find('.hysteryale_updater__config_row').each(function() {
                var $this = $(this),
                    import_config = $this.find('.hysteryale_updater__config_import').val(),
                    $wp_id = $this.find('.hysteryale_updater__config_existing');

                if (import_config == 'update') {
                    //$wp_id.show();
                    $wp_id.prop('disabled', false);
                } else {
                    //$wp_id.hide();
                    $wp_id.prop('disabled', true);
                }
                    
            });
        },

        start: function(event) {
            event.preventDefault();

            $(this).prop('disabled', true);

            var progress = new HYSTERYALE_UPDATER.Progress();
                progress.start();

            var wp_action = 'hysteryale_updater__import',
                $form = $('#hysteryale_updater__import_config'),
                config = {};

            $form.find('.hysteryale_updater__config_row').each(function() {
                var $this = $(this),
                    hysteryale_id = $this.find('.hysteryale_updater__config_hysteryale').val();

                config[hysteryale_id] = {
                    hysteryale_id: hysteryale_id,
                    type: $this.find('.hysteryale_updater__config_type').val(),
                    import_config: $this.find('.hysteryale_updater__config_import').val(),
                    wp_id: $this.find('.hysteryale_updater__config_existing').val()
                }
                    
            });

            $.ajax({
                url: ajaxurl
                ,type: 'post'
                ,data: {
                    action: wp_action,
                    config: config
                }
            }).done(function(){
                progress.stop();
            }).always(function(){
                // Show Alert
                $('.importer-buttons').prepend('<div id="message" class="updated notice notice-success is-dismissible below-h2"><p>Import complete. Reloading product config...</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
                //refresh page
                document.location.reload()
            });
        }
    };


    /**
     * Class for controlling progress
     */

    HYSTERYALE_UPDATER.Progress = function() {
        this.el = null;
        this.timer = null;
        this.delay = 1000;
        this.percent = 0;
        this.text = '';
        this.template = '<progress class="progress__bar js-progress-bar" value="0" max="100"></progress>'
                      + '<span class="progress__text js-progress-text"></span>';
    }

    HYSTERYALE_UPDATER.Progress.prototype.start = function() {
        var that = this;

        that.el    = $('.progress').append(that.template);
        that.timer = setInterval(function() {
            that.send();
        }, that.delay );
    };

    HYSTERYALE_UPDATER.Progress.prototype.stop = function() {
        clearInterval(this.timer);
        this.el.empty();
    };

    HYSTERYALE_UPDATER.Progress.prototype.send = function() {
        $.ajax({
            url: ajaxurl,
            type: "post",
            data: { action: 'hysteryale_updater__progress_poll' },
            dataType: "json"
        }).done($.proxy(this.update, this));
    }

    HYSTERYALE_UPDATER.Progress.prototype.update = function( data ) {
        if(typeof data !== "undefined")
        {
            this.percent =  Math.floor((data.indexed/data.total)*100);
            this.text    = data.text;

            this.el.children('.js-progress-bar').val(this.percent);
            this.el.children('.js-progress-text').html(data.text + ' - ' + this.percent+'%');
        }
    }

    $(function(){
        wp.HYSTERYALE_UPDATER.Importer.init();
    });

    return HYSTERYALE_UPDATER;
})(wp.HYSTERYALE_UPDATER || {}, jQuery)
