jQuery(document).ready(function ($) {

    // step one 
    $(".dms_import_btn").on('click', function (e) {
        e.preventDefault();

        var xml_link = $(this).attr("data-xml_link");
        var xml_data = $(this).attr('data-required-plugin');
        var nonce = $(this).attr("data-nonce");
        var name = $(this).attr("data-name");
        var required_plugin = $(this).data('required-plugin');

        $('#dms-importMmodal').modal('show');

        $.ajax({
            type: "post",
            dataType: "json",
            url: dmsAjax.ajaxurl,
            data: {
                action: "devm_import_config",
                xml_link: xml_link,
                xml_data: xml_data,
                nonce: nonce,
                name: name
            },
            success: function (response) {
                console.log("success");
                var config = {
                        ...response.data,
                        required_plugin
                    },
                    output = required_plugin.map(item => "<p id=" + item + ">" + item + "</p>");
                $('.dms-continue-btn').attr('config', JSON.stringify(config));
                $('.dms-importer-plugin-list').html(output);
            },
            error: function (error) {
                console.log(error);
            }
        });


    });

    // First Step activation
    $('.dms-single-importer').first().addClass('active');
    $('.dms-single-content--preview-img').first().addClass('active');
    // close modal
    $(".dms-close-btn").on('click', function (e) {
        e.preventDefault();
        $('.dms-single-importer').removeClass('active').first().addClass('active');
        $('.dms-single-content--preview-img').removeClass('active').first().addClass('active');
        // Reset data
        $('.dms-importer-data').css('transform', 'translateX(0)');
        $('.dms-importer-final-buttons').hide();
        $('.dms-importer-normal-buttons').show();
        $('.dms-progress-bar .attr-progress-bar').css('width', '');
        $('.form-check-input').prop('checked', false);
    });

    // Skip step
    $('.dms-skip-btn').on('click', function (e) {
        e.preventDefault();
        var parent = $(this).parents('.dms-single-content');
        stepSection = parent.find('.dms-single-importer.active'),
            checkbox = parent.find('.form-check-input');

        // section active
        stepSection.removeClass('active').next().addClass('active');
        checkbox.is(':checked') ? checkbox.prop('checked', false) : '';

        // slide section
        var transformValue = parent.find('.dms-single-importer.active').index() * 100;
        parent.find('.dms-importer-data').css('transform', 'translateX(-' + transformValue + '%)');

        // show final_step buttons
        if (stepSection.data('step') == 'content_import') {
            $('.dms-importer-final-buttons').show();
            $('.dms-importer-normal-buttons').hide();
        }
    })

    // content install 
    $('.dms-continue-btn').on('click', function (e) {
        e.preventDefault();

        var self = $(this),
            parent = self.parents('.dms-modal-main-content'),
            step = self.parents('.dms-single-content').find('.dms-single-importer.active').data('step'),
            config_data = JSON.parse($(this).attr('config')),
            nonce = config_data.nonce,
            dms_delete_data = false
        required_plugin = config_data.required_plugin;
        // active section
        self.addClass('active').siblings().removeClass('active');

        // delete dms data if checked
        if (parent.find('#dms_delete_data_confirm').is(':checked')) {
            dms_delete_data = true
        }

        // config
        var config = {
            'dms_delete_data': dms_delete_data,
            'required_plugin': required_plugin,
            'xml_link': config_data
        };

        // Erasing Content
        if (parent.find('.form-check-input').is(":checked") && step === 'erase') {
            dms_demo_content_install('dms_import_erase_data', nonce, config, step, parent);
        } else if (step === 'plugin_install') { // Plugin Installing
            if (config_data.required_plugin) {
                config_data.required_plugin.forEach(function (item) {
                    config.required_plugin = [item];
                    dms_demo_content_install('dms_import_plugin_install', nonce, config, step, parent);
                });
            }

        } else if (step === 'content_import') { // Content Importing
            dms_demo_content_install('dms_import_demo', nonce, config, step, parent);
        } else {
            $('.dms-btn, .dms-progress-bar').removeClass('success');
            $('.dms-importer-data--welcome-title').removeClass('hidden').removeClass('start');
            parent.find('.dms-importer-data--progress-msg').fadeOut();
            $(this).parents('.dms-single-content').find('.dms-single-importer.active').removeClass('active').next().addClass('active');

            var transformValue = $(this).parents('.dms-single-content').find('.dms-single-importer.active').index() * 100;
            parent.find('.dms-importer-data').css('transform', 'translateX(-' + transformValue + '%)');

            // Slide Image
            parent.find('.dms-single-content--preview-img').eq(parent.find('.dms-single-importer.active').index()).addClass('active')
                .siblings().removeClass('active');
        }


    });

    function dms_demo_content_install(action, nonce, config = null, step = null, parent = null) {
        let required_plugin = config.required_plugin;

        $.ajax({
            async: true,
            type: "post",
            dataType: "json",
            url: dmsAjax.ajaxurl,
            data: {
                action: action,
                nonce: nonce,
                config: config

            },
            beforeSend: function (xhr) {
                parent.find('.dms-btn').prop('disabled', true);
                parent.find('.dms-btn').addClass('show');
                if (step == 'content_import') {
                    parent.find('.dms-loading').addClass('start');
                    parent.find('.dms-importer-data--progress-msg').fadeToggle();
                    parent.find('.dms-single-importer.active .dms-importer-data--welcome-title').addClass('hidden');
                }
            },
            success: function (response) {

                let delay = 0;
                parent.find('.dms-btn').removeClass('active').removeClass('show');
                parent.find('.dms-btn').prop('disabled', false);


                if (step == 'plugin_install') {
                    delay = null;
                    let required_plugin_list = JSON.parse(parent.find(".dms-continue-btn").attr('config')).required_plugin;

                    parent.find('.dms-importer-plugin-list p#' + required_plugin).addClass('dms-installed');
                    var installedPlugin = parent.find('.dms-single-importer.active .dms-installed'),
                        parcent = (installedPlugin.length * 100) / required_plugin_list.length;

                    if (required_plugin_list.length === installedPlugin.length) {
                        parent.find('.dms-progress-bar').addClass('success');
                        delay = 850;
                    }

                    parent.find('.dms-progress-bar .attr-progress-bar').css('width', parcent + '%');

                } else if (step == 'content_import') {
                    parent.find('.dms-loading').removeClass('start');
                    $('.dms-importer-final-buttons').show();
                    $('.dms-importer-normal-buttons').hide();
                }


                if (delay == null) {
                    return;
                }
                setTimeout(function () {
                    parent.find('.dms-single-importer.active').removeClass('active').next().addClass('active');
                    var transformValue = parent.find('.dms-single-importer.active').index() * 100;
                    parent.find('.dms-importer-data').css('transform', 'translateX(-' + transformValue + '%)');

                    parent.find('.dms-single-content--preview-img').eq(parent.find('.dms-single-importer.active').index()).addClass('active')
                        .siblings().removeClass('active');
                }, delay)
            },
            error: function (error) {
                console.log(error);
            }
        });
    }
    // erase data alert    
    $("#dms_delete_data_confirm").change(function () {
        if (this.checked) {
            alert("Do you want to delete prev data?");
        }
    });

})