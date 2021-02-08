jQuery(document).ready(function ($) {

    // step one 
    $(".devm_import_btn").on('click', function (e) {
        e.preventDefault();

        var xml_link = $(this).attr("data-xml_link");
        var xml_data = $(this).attr('data-required-plugin');
        var nonce = $(this).attr("data-nonce");
        var name = $(this).attr("data-name");
        var required_plugin = $(this).data('required-plugin');
        var xml_selected_demo   = $(this).data('selected_demo');

        $('#devm-importMmodal').modal('show');

        $.ajax({
            type: "post",
            dataType: "json",
            url: devmAjax.ajaxurl,
            data: {
                action: "devm_import_config",
                xml_link: xml_link,
                xml_data: xml_data,
                xml_selected_demo: xml_selected_demo,
                nonce: nonce,
                name: name
            },
            success: function (response) {
                var config = {
                        ...response.data,
                        required_plugin
                    },
                    output = required_plugin.map(item => "<p id=" + item.slug + ">" + item.slug + "</p>");
                $('.devm-continue-btn').attr('config', JSON.stringify(config));
                $('.devm-importer-plugin-list').html(output);
            },
            error: function (error) {
                console.log(error);
            }
        });


    });

    // First Step activation
    $('.devm-single-importer').first().addClass('active');
    $('.devm-single-content--preview-img').first().addClass('active');
    // close modal
    $(".devm-close-btn").on('click', function (e) {
        e.preventDefault();
        $('.devm-single-importer').removeClass('active').first().addClass('active');
        $('.devm-single-content--preview-img').removeClass('active').first().addClass('active');
        // Reset data
        $('.devm-importer-data').css('transform', 'translateX(0)');
        $('.devm-importer-final-buttons').hide();
        $('.devm-importer-normal-buttons').show();
        $('.devm-progress-bar .attr-progress-bar').css('width', '');
        $('.form-check-input').prop('checked', false);
    });

    // Skip step
    $('.devm-skip-btn').on('click', function (e) {
        e.preventDefault();
        var parent = $(this).parents('.devm-single-content');
        stepSection = parent.find('.devm-single-importer.active'),
            checkbox = parent.find('.form-check-input');

        // section active
        stepSection.removeClass('active').next().addClass('active');
        checkbox.is(':checked') ? checkbox.prop('checked', false) : '';

        // slide section
        var transformValue = parent.find('.devm-single-importer.active').index() * 100;
        parent.find('.devm-importer-data').css('transform', 'translateX(-' + transformValue + '%)');

        // show final_step buttons
        if (stepSection.data('step') == 'content_import') {
            $('.devm-importer-final-buttons').show();
            $('.devm-importer-normal-buttons').hide();
        }
    })

    // content install 
    $('.devm-continue-btn').on('click', function (e) {
        e.preventDefault();

        var self = $(this),
            parent = self.parents('.devm-modal-main-content'),
            step = self.parents('.devm-single-content').find('.devm-single-importer.active').data('step'),
            config_data = JSON.parse($(this).attr('config')),
            nonce = config_data.nonce,
            devm_delete_data = false
        required_plugin = config_data.required_plugin;
        selected_demo   = config_data.selected_demo;
        // active section
        self.addClass('active').siblings().removeClass('active');

        // delete devm data if checked
        if (parent.find('#devm_delete_data_confirm').is(':checked')) {
            devm_delete_data = true
        }

        // config
        var config = {
            'devm_delete_data': devm_delete_data,
            'required_plugin': required_plugin,
            'selected_demo': selected_demo,
            'xml_link': config_data
        };

        // Erasing Content
        if (parent.find('.form-check-input').is(":checked") && step === 'erase') {
            devm_demo_content_install('devm_import_erase_data', nonce, config, step, parent);
        } else if (step === 'plugin_install') { // Plugin Installing
            if (config_data.required_plugin) {
                config_data.required_plugin.forEach(function (item) {
                    config.required_plugin = [item];
                    devm_demo_content_install('devm_import_plugin_install', nonce, config, step, parent);
                });
            }

        } else if (step === 'content_import') { // Content Importing
            devm_demo_content_install('devm_import_demo', nonce, config, step, parent);
        } else {
            $('.devm-btn, .devm-progress-bar').removeClass('success');
            $('.devm-importer-data--welcome-title').removeClass('hidden').removeClass('start');
            parent.find('.devm-importer-data--progress-msg').fadeOut();
            $(this).parents('.devm-single-content').find('.devm-single-importer.active').removeClass('active').next().addClass('active');

            var transformValue = $(this).parents('.devm-single-content').find('.devm-single-importer.active').index() * 100;
            parent.find('.devm-importer-data').css('transform', 'translateX(-' + transformValue + '%)');

            // Slide Image
            parent.find('.devm-single-content--preview-img').eq(parent.find('.devm-single-importer.active').index()).addClass('active')
                .siblings().removeClass('active');
        }


    });

    function devm_demo_content_install(action, nonce, config = null, step = null, parent = null) {
        let required_plugin = config.required_plugin;

        $.ajax({
            async: true,
            type: "post",
            dataType: "json",
            url: devmAjax.ajaxurl,
            data: {
                action: action,
                nonce: nonce,
                config: config

            },
            beforeSend: function (xhr) {
                parent.find('.devm-btn').prop('disabled', true);
                parent.find('.devm-btn').addClass('show');
                if (step == 'content_import') {
                    parent.find('.devm-loading').addClass('start');
                    parent.find('.devm-importer-data--progress-msg').fadeToggle();
                    parent.find('.devm-single-importer.active .devm-importer-data--welcome-title').addClass('hidden');
                }
            },
            success: function (response) {

                let delay = 0;
                parent.find('.devm-btn').removeClass('active').removeClass('show');
                parent.find('.devm-btn').prop('disabled', false);


                if (step == 'plugin_install') {
                    delay = null;
                    let required_plugin_list = JSON.parse(parent.find(".devm-continue-btn").attr('config')).required_plugin;

                    parent.find('.devm-importer-plugin-list p#' + required_plugin[0].slug).addClass('devm-installed');
                    var installedPlugin = parent.find('.devm-single-importer.active .devm-installed'),
                        parcent = (installedPlugin.length * 100) / required_plugin_list.length;

                    if (required_plugin_list.length === installedPlugin.length) {
                        parent.find('.devm-progress-bar').addClass('success');
                        delay = 850;
                    }

                    parent.find('.devm-progress-bar .attr-progress-bar').css('width', parcent + '%');

                } else if (step == 'content_import') {
                    parent.find('.devm-loading').removeClass('start');
                    $('.devm-importer-final-buttons').show();
                    $('.devm-importer-normal-buttons').hide();
                }

                if (delay == null) {
                    return;
                }
                setTimeout(function () {
                    parent.find('.devm-single-importer.active').removeClass('active').next().addClass('active');
                    var transformValue = parent.find('.devm-single-importer.active').index() * 100;
                    parent.find('.devm-importer-data').css('transform', 'translateX(-' + transformValue + '%)');

                    parent.find('.devm-single-content--preview-img').eq(parent.find('.devm-single-importer.active').index()).addClass('active')
                        .siblings().removeClass('active');
                }, delay)
            },
            error: function (error) {
                console.log(error);
            }
        });
    }
    // erase data alert    
    $("#devm_delete_data_confirm").change(function () {
        if (this.checked) {
            alert("Do you want to delete prev data?");
        }
    });

})