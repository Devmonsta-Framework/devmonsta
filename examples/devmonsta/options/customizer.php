<?php

class Customizer extends \Devmonsta\Libs\Customizer {

    public function register_controls() {

        /**
         * Add parent panels
         */
        $this->add_panel( [
            'id'             => 'devmonsta_panel',
            'priority'       => 0,
            'theme_supports' => '',
            'title'          => __( 'Devmonsta panel', 'devmonsta' ),
            'description'    => __( 'Devmonsta options panel', 'devmonsta' ),
        ] );

        $this->add_panel([
            'id'             => 'banner',
            'title'          => __('banner settings', 'sassico'),
            'panel'          => 'devmonsta_panel',
        ]);

        $this->add_section([
            'id'       => 'abc',
            'title'    => __('General Settings', 'sassico'),
            'panel'    => 'banner',
            'priority' => 10,
        ]);

        $this->add_control([
            'id'      => 'logo',
            'type'    => 'media',
            'description' => 'some descriptin',
            'section' => 'abc',
            'label'   => esc_html__('Main Logo', 'sassico'),
            'desc'   => esc_html__("It's the main logo, mostly it will be shown on dark or coloreful type area.
            ", 'sassico'),
        ]);

        $this->add_panel([
            'id'             => 'l_2',
            'title'          => 'Level 2',
            'panel'          => 'devmonsta_panel',
        ]);

        $this->add_panel([
            'id'        => 'l_3',
            'title'     => 'Level 3',
            'panel'     => 'l_2',
        ]);

        /**
         * Sections will go inside panel
         * sections will hold controls
         */


        $this->add_section( [
            'id'       => 'devmonsta_text_settings_section',
            'title'    => __( 'Text settings', 'devmonsta' ),
            'panel'    => 'devmonsta_panel',
            'priority' => 10,
        ] );

        $this->add_section( [
            'id'       => 'devm_repeater_section',
            'title'    => 'Devmonsta repeater section',
            'panel'    => 'devmonsta_panel',
            'priority' => 10,
        ] );

        $this->add_section( [
            'id'       => 'devm_new_controls',
            'title'    => 'New controls',
            'panel'    => 'devmonsta_panel',
            'priority' => 10,
        ] );



        $this->add_section([
            'id'       => 'test_level',
            'title'    => 'Level',
            'panel'    => 'l_3',
        ]);

        $this->add_control([
            'id'       => 'lvl_control_1',
            'label'    => 'Text',
            'type'     => 'text',
            'section'  => 'test_level',
        ]);

        /**
         * ===========================================
         *      Customizer default control start
         * ===========================================
         */

        /**
         * Control for text input
         */
        $this->add_control( [
            'id'      => 'devm_ctrl_text_1',
            'type'    => 'text',
            'label'   => __( 'Devmonsta default Text Label', 'devmonsta' ),
            'description' => __( 'This is a Text input desc.', 'devmonsta' ),
            "default"   => "hello hello chay pilo",
            'section' => 'devmonsta_text_settings_section',
        ] );

        /**
         * control for checkbox input
         */
        $this->add_control( [
            'id'      => 'devm_checkbox',
            'section' => 'devmonsta_text_settings_section',
            'type'    => 'checkbox',
            'default'   => true, // checked/unchecked
            'label'   => __( 'Devmonsta default Checkbox label', '{domain}' ),
            'description' => __( 'This is a Checkbox input desc.', 'devmonsta' ),
            'text'    => __( 'Yes', '{domain}' ),
        ] );

        /**
         * control for radio input
         */
        $this->add_control( [
            'type'        => 'radio',
            'id'          => 'devm_test_readio',
            'label'       => __( 'Radio Selection label', 'devmonsta' ),
            'description' => __( 'This is a custom radio input desc.', 'devmonsta' ),
            'default'   => 'blue',
            'choices'     => [
                'red'   => __( 'Red' ),
                'blue'  => __( 'Blue' ),
                'green' => __( 'Green' ),
            ],
            'section'     => 'devmonsta_text_settings_section',
        ] );

        /**
         * control for dropdown select
         */
        $this->add_control( [
            'id'      => 'select',
            'section' => 'devmonsta_text_settings_section',
            'type'    => 'select',
            'default'   => 'choice-1',
            'label'   => __( 'Select Single label', '{domain}' ),
            'description'    => __( 'select description goes here', '{domain}' ),
            'choices' => [
                ''         => '---',
                'choice-1' => __( 'Choice One', '{domain}' ),
                'choice-2' => __( 'Choice Two', '{domain}' ),
                'choice-3' => __( 'Choice Three', '{domain}' ),
            ],
        ] );

        /**
         * control for textarea input
         */
        $this->add_control( [
            'id'          => 'devm_textarea',
            'type'        => 'textarea',
            'section'     => 'devmonsta_text_settings_section',
            'label'       => __( 'Text area label' ),
            'description' => __( 'This is text area desctription', 'devmonsta' ),
            "default"       => 'default value for text area',
        ] );

        /**
         * Control for dropdown-page input
         */
        $this->add_control( [
            'id'          => 'devm_dropdown_pages',
            'type'        => 'dropdown-pages',
            'section'     => 'devmonsta_text_settings_section', // Add a default or your own section
            'label'       => __( 'Custom Dropdown Pages label', 'devmonsta' ),
            'description' => __( 'This is a custom dropdown pages option.' ),
        ] );

        /**
         * control for url input
         */
        $this->add_control( [
            'id'      => 'devm_url',
            'section' => 'devmonsta_text_settings_section',
            'type'    => 'url',
            'default'   => 'http://www.xs.com',
            'label'   => __( 'Enter valid URL label', '{domain}' ),
            'description'    => __( 'Url Description', '{domain}' ),
        ] );


        /**
         * Control for media-select input
         */
        $this->add_control( [
            'id'      => 'devm_media',
            'type'    => 'media',
            'section' => 'devmonsta_text_settings_section',
            'label'   => __( 'Media label', 'devmonsta' ),
            'description' => __( 'Media description goes here', 'devmonsta' ),
            "default" => 21
        ] );

        /**
         * ===========================================
         *      Default control end
         * ===========================================
         */



        /**
         * ===========================================
         *      Custom control start
         * ===========================================
         */


        /**
         * control for date-picker input
         */
        $this->add_control( [
            'id'           => 'start_date',
            'section'      => 'devmonsta_text_settings_section',
            'type'         => 'date-picker',
            'value'        => '2020/05/10',
            'label'        => __( 'Date Picker', '{domain}' ),
            'desc'         => __( 'date picker description goes here', '{domain}' ),
            'monday-first' => true,         // The week will begin with Monday; for Sunday, set to false
            'min-date'     => "10-05-2020", // By default minimum date will be current day. Set a date in format Y-m-d as a start date
            'max-date'     => null,         // By default there is not maximum date. Set a date in format Y-m-d as a start date
        ] );

        /**
         * control for datetime-picker input
         */
        $this->add_control( [
            'id'              => 'devm_date_time',
            'section'         => 'devmonsta_text_settings_section',
            'type'            => 'datetime-picker',
            'label'           => __( 'Date Time Picker', '{domain}' ),
            'value'           => '10-05-2020 12:00',
            'desc'            => __( 'date time picker description', '{domain}' ),
            'datetime-picker' => [
                'date-format'  => 'Y-m-d H:i',              // Format datetime.
                'time-24'      => false,
                'min-date'     => "10-05-2020 12:00", // By default minimum date will be current day. Set a date in format Y-m-d as a start date
                'max-date'     => null,               // By default there is not maximum date. Set a date in format Y-m-d as a start date
                'timepicker'   => true,               // Show timepicker.
                'default-time' => '12:00',            // If the input value is empty, timepicker will set time use defaultTime.
            ],
        ] );

        /**
         * control for datetime-range input
         */
        $this->add_control( [
            'id'              => 'date_time_range_picker',
            'section'         => 'devmonsta_text_settings_section',
            'type'            => 'datetime-range',
            'value'           => [
                'from' => '10-05-2020 12:00',
                'to'   => '15-05-2020 04:00',
            ],
            'label'           => __( 'Date Time Range Picker', '{domain}' ),
            'desc'            => __( 'date time range picker description', '{domain}' ),
            'datetime-picker' => [
                'date-format'  => 'Y-m-d H:i',              // Format datetime.
                'time-24'      => true,
                'min-date'    => "10-05-2020 12:00", // By default minimum date will be current day. Set a date in format Y-m-d as a start date
                'max-date'    => null,               // By default there is not maximum date. Set a date in format Y-m-d as a start date
                'timepicker'  => true,               // Show timepicker.
                'defaultTime' => '12:00',            // If the input value is empty, timepicker will set time use defaultTime.
            ],
        ] );

        /**
         * control for dimension input
         */
        $this->add_control( [
            'id'      => 'padding_dimension',
            'section' => 'devmonsta_text_settings_section',
            'type'    => 'dimensions',
            'label'   => __( 'Dimension Input', '{domain}' ),
            'desc'    => __( 'Dimension text description', '{domain}' ),
            'value'   => [
                'top'      => '2',
                'right'    => '3',
                'bottom'   => '4',
                'left'     => '56',
                'isLinked' => true,
            ],
        ] );

        /**
         * Control for switcher input
         */
        $this->add_control( [
            'section'      => 'devmonsta_text_settings_section',
            'type'         => 'switcher',
            'id'           => 'devm_switcher',
            'value'        => 'hello',
            'label'        => __( 'Switcher', '{domain}' ),
            'desc'         => __( 'Description', '{domain}' ),
            'left-choice'  => [
                'goodbye' => __( 'Go Now', '{domain}' ),
            ],
            'right-choice' => [
                'hello' => __( 'Hi', '{domain}' ),
            ],
            'attr'         => ['class' => 'custom-class', 'data-foo' => 'bar']
        ] );

        /**
         * control for multiple-checkbox input
         */
        $this->add_control( [
            'section' => 'devmonsta_text_settings_section',
            'id'      => 'multiple_checkboxes',
            'type'    => 'checkbox-multiple',
            'value'   => [
                'choice-1' => true,
                'choice-2' => true,
            ],
            'attr'    => ['class' => 'custom-class', 'data-foo' => 'bar'],
            'label'   => __( 'Multiple Chekbox', '{domain}' ),
            'desc'    => __( 'Multi checkbox Description', '{domain}' ),
            'choices' => [
                'choice-1' => __( 'Choice 1', '{domain}' ),
                'choice-2' => __( 'Choice 2', '{domain}' ),
                'choice-3' => __( 'Choice 3', '{domain}' ),
            ],
            'inline'  => false,
        ] );

        /**
         * Control for Image picker Input
         */
        $this->add_control( [
            'id'      => 'i_p',
            'section' => 'devmonsta_text_settings_section',
            'type'    => 'image-picker',
            'value'   => 'value-5',
            'label'   => __( 'Thumbnail Image Picker', '{domain}' ),
            'desc'    => __( 'Description', '{domain}' ),
            'choices' => [
                'value-1' => [
                    'small' => get_template_directory_uri() . '/images/thumbnail.jpg',
                    'large' => get_template_directory_uri() . '/images/thumbnail.jpg',
                ],
                'value-2' => [
                    'small' => get_template_directory_uri() . '/images/preview.png',
                    'large' => get_template_directory_uri() . '/images/preview.png',
                ],
                'value-3' => [
                    'small' => get_template_directory_uri() . '/images/a.jpg',
                    'large' => get_template_directory_uri() . '/images/a.jpg',
                ],
                'value-4' => [
                    'small' => get_template_directory_uri() . '/images/b.jpg',
                    'large' => get_template_directory_uri() . '/images/b.jpg',
                ],
                'value-5' => [
                    'small' => get_template_directory_uri() . '/images/c.jpg',
                    'large' => get_template_directory_uri() . '/images/c.jpg',
                ],
            ],
        ] );

        /**
         * Control for Html input
         */
        $this->add_control( [
            'id'      => 'html',
            'section' => 'devmonsta_text_settings_section',
            'label'   => __( 'Html Input', '{domain}' ),
            'desc'    => __( 'html description goes here', '{domain}' ),
            'type'    => 'html',
            'value'   => 'My <b>custom</b> <em>HTML</em> <i>Italic<i> <p>Paragraph</p>',
        ] );

        /**
         * control for gradient input
         */
        $this->add_control( [
            'id'         => 'gradient',
            'section'    => 'devmonsta_text_settings_section',
            'type'       => 'gradient',
            'label'      => __( 'Wp Gradient Picker Example', '{domain}' ),
            'desc'       => __( 'description of gradient-picker goes here', '{domain}' ),
            'value'      => [
                'primary'   => '#FF00FF',
                'secondary' => '#0000FF',
            ]
        ] );

        /**
         * control for multiple select
         */
        $this->add_control( [
            'id'      => 'select_multiple',
            'section' => 'devmonsta_text_settings_section',
            'type'    => 'multiselect',
            'label'   => __( 'Select Multiple', '{domain}' ),
            'desc'    => __( 'multiple select description goes here', '{domain}' ),
            'value'   => [
                'choice-1',
                'choice-2',
            ],
            'choices' => [
                'choice-1' => __( 'Choice One', '{domain}' ),
                'choice-2' => __( 'Choice Two', '{domain}' ),
                'choice-3' => __( 'Choice Three', '{domain}' ),
            ],
        ] );

        /**
         * control for color-picker input
         */
        $this->add_control( [
            'id'      => 'color_one',
            'section' => 'devmonsta_text_settings_section',
            'type'     => 'color-picker',
            'label'    => __( 'Wp Color Picker One', '{domain}' ),
            'desc'     => __( 'description of color-picker goes here', '{domain}' ),
            'value'    => '#FF0000',
            'palettes' => ['#ba4e4e', '#0ce9ed', '#941940'],
        ] );

        /**
         * control for oembed input
         */
        $this->add_control( [
            'id'      => 'oembed_field',
            'section' => 'devmonsta_text_settings_section',
            'type'    => 'oembed',
            'label'   => __( 'Oembed Input', '{domain}' ),
            'desc'    => __( 'Oembed text description', '{domain}' ),
            'attr'    => ['class' => 'custom-class', 'data-foo' => 'bar'],
            'value'   => 'https://www.youtube.com/watch?v=0Nh11GI4-Gc',
            // 'value'   => 'https://soliloquywp.com/wordpress-best-practices/',
            'preview' => [
                'width'      => 200, // optional, if you want to set the fixed width to iframe
                'height'     => 100, // optional, if you want to set the fixed height to iframe
                'keep_ratio' => true,
            ],
        ] );

        /**
         * control for wp-editor input
         */
        $this->add_control( [
            'id'            => 'wp_editor',
            'section'       => 'devmonsta_text_settings_section',
            'type'          => 'wp-editor',
            'value'         => 'default value',
            'label'         => __( 'Wp Editor Example', '{domain}' ),
            'desc'          => __( 'description of wp-editor goes here', '{domain}' ),
            'editor_height' => 300,
            'wpautop'       => true,
            'editor_type'   => false,
        ] );

        /**
         * control for rgba-color-picker input
         */
        $this->add_control( [
            'id'       => 'rgba_color',
            'section'  => 'devmonsta_text_settings_section',
            'type'     => 'rgba-color-picker',
            'label'    => __( 'Wp RGBA Color Picker Example', '{domain}' ),
            'desc'     => __( 'description of rgba-color-picker goes here', '{domain}' ),
            'value'    => 'rgba(255,20,0,0.95)',
            'palettes' => ['#ba4e4e', '#5f9419', '#381994'],
        ] );

        /**
         * control for icon-picker input
         */
        $this->add_control( [
            'id'      => 'icon_picker',
            'section' => 'devmonsta_text_settings_section',
            'type'    => 'icon',
            'value'  => [
                'icon' => 'fas fa-at',
                'type' => 'devm-font-awesome',
            ],
            'label'   => __( 'Select Icon', '{domain}' ),
            'desc'    => __( 'Select icon description', '{domain}' ),
            'attr'    => ['class' => 'custom-class', 'data-foo' => 'bar'],
        ] );

        /**
         * control for slider input
         */
        $this->add_control( [
            'type'       => 'slider',
            'id'         => 'slider_widget',
            'section'    => 'devmonsta_text_settings_section',
            'label'      => __( 'Wp Slider Example', '{domain}' ),
            'desc'       => __( 'description of slider goes here', '{domain}' ),
            'value'      => 68,
            'properties' => [
                'min'  => 0,
                'max'  => 100,
                'step' => 1,
            ],
        ] );

        /**
         * control for range-slider input
         */
        $this->add_control( [
            'id'         => 'range_slider_widget',
            'section'    => 'devmonsta_text_settings_section',
            'type'       => 'range-slider',
            'label'      => __( 'Wp Range Slider Example', '{domain}' ),
            'desc'       => __( 'description of range slider goes here', '{domain}' ),
            'value'      => [
                'from' => 20,
                'to'   => 33,
            ],
            'properties' => [
                'min'  => 0,
                'max'  => 100,
                'step' => 1,
            ],
        ] );

        /**
         * Control for Typography Input
         */
        $this->add_control( [
            'id'         => 'typo',
            'section'    => 'devmonsta_text_settings_section',
            'type'       => 'typography',
            'value'      => [
                'family'         => 'Amarante',
                'style'          => 'italic',
                'weight'         => 700,
                'subset'         => 'latin-ext',
                'variation'      => 'regular',
                'size'           => 14,
                'line_height'    => 13,
                'letter_spacing' => -2,
                'color'          => '#FF0000',
            ],
            'components' => [
                'family'         => true,
                'size'           => true,
                'line-height'    => true,
                'letter-spacing' => true,
                'color'          => true,
            ],
            'label'      => __( 'Typhography', '{domain}' ),
            'desc'       => __( 'Description', '{domain}' ),
        ] );

        /**
         * ===========================================
         *      Custom control end
         * ===========================================
         */

        // New controls

        $this->add_control( [
            'id'      => 'devm_toggle',
            'label'   => __( 'Toggle', 'devmonsta' ),
            'section' => 'devm_new_controls',
            'type'    => 'toggle',
        ] );

        $this->add_control( [
            'id'      => 'devm_accordion',
            'lable'   => __( 'Accordion', 'devmonsta' ),
            'section' => 'devm_new_controls',
            'type'    => 'toggle',
        ] );

        $this->add_control( [
            'id'          => 'devm_accordion',
            'lable'       => __( 'Accordion', 'devmonsta' ),
            'section'     => 'devm_new_controls',
            'type'        => 'accordion',
            'description' => [
                'Title' => 'Sotry fo title',
            ],
        ] );

        $this->add_control( [
            'id'          => 'devm_html_editor',
            'lable'       => __( 'HTML Editor', 'devmonsta' ),
            'section'     => 'devm_new_controls',
            'type'        => 'html-editor',
        ] );

        $this->add_control( [
            'id'              => 'devmonsta_repeater_control',
            'label'           => 'List',
            'type'            => 'repeater',
            'section'         => 'devm_repeater_section',
            'add_button_text' => __( 'Add new', 'devmonsta' ),
            'fields'          => [
                [
                    'id'    => 'person_name',
                    'label' => __( 'Name', 'devmonsta' ),
                    'type'  => 'text',
                ],
                [
                    'id'    => 'person_email',
                    'label' => __( 'Email', 'devmonsta' ),
                    'type'  => 'email',
                ],
                [
                    'id'    => 'person_birht_date',
                    'label' => __( 'Date of birth', 'devmonsta' ),
                    'type'  => 'date',
                ],

                [
                    'id'    => 'person_child',
                    'label' => __( 'Child', 'devmonsta' ),
                    'type'  => 'number',
                ],
                [
                    'id'    => 'person_color',
                    'label' => __( 'Awesome Color', 'devmonsta' ),
                    'type'  => 'color',
                ],
                [
                    'id'    => 'person_hair_color',
                    'label' => __( 'Hair Color', 'devmonsta' ),
                    'type'  => 'color',
                ],
                [
                    'id' => 'icon_picker_repeater',
                    'label' => 'Icon picker',
                    'type' => 'icon',
                ],
                [
                    'id'    => 'person_image',
                    'type'  => 'media',

                    'label' => __( 'Media' ),

                ],
                [
                    'id'          => 'devm_sum_test_control_kk',
                    'type'        => 'test-control',
                    'section'     => 'devmonsta_text_settings_section', // Add a default or your own section
                    'label'       => __( 'Custom Dropdown Pages' ),
                    'description' => __( 'This is a custom dropdown pages option.' ),
                ],
                [
                    'type'        => 'radio',
                    'id'          => 'devm_test_readio_for_repeater',
                    'label'       => __( 'Custom Radio Selection' ),
                    'description' => __( 'This is a custom radio input.' ),
                    'choices'     => [
                        'red'   => __( 'Red' ),
                        'blue'  => __( 'Blue' ),
                        'green' => __( 'Green' ),
                    ],
                ],

            ],
        ] );

        $this->add_control( [
            'id'              => 'devmonsta_repeater_popup_control',
            'label'           => 'Popup',
            'type'            => 'addable-popup',
            'section'         => 'devm_repeater_section',
            'add_button_text' => __( 'Add new', 'devmonsta' ),
            'fields'          => [
                [
                    'id'    => 'person_name_popup',
                    'label' => __( 'Name', 'devmonsta' ),
                    'type'  => 'text',
                ],
                [
                    'id'    => 'person_email_popup',
                    'label' => __( 'Email', 'devmonsta' ),
                    'type'  => 'email',
                ],
                [
                    'id'    => 'person_birht_date_popup',
                    'label' => __( 'Date of birth', 'devmonsta' ),
                    'type'  => 'date',
                ],

                [
                    'id'    => 'person_child_popup',
                    'label' => __( 'Child', 'devmonsta' ),
                    'type'  => 'number',
                ],
                [
                    'id'    => 'person_color_popup',
                    'label' => __( 'Awesome Color', 'devmonsta' ),
                    'type'  => 'color',
                ],
                [
                    'id'    => 'person_hair_color_popup',
                    'label' => __( 'Hair Color', 'devmonsta' ),
                    'type'  => 'color',
                ],
                [
                    'id'    => 'person_image_popup',
                    'type'  => 'media',

                    'label' => __( 'Media' ),

                ],
                [
                    'id'          => 'devm_sum_test_control_kk_popup',
                    'type'        => 'test-control',
                    'section'     => 'devmonsta_text_settings_section', // Add a default or your own section
                    'label'       => __( 'Custom Dropdown Pages' ),
                    'description' => __( 'This is a custom dropdown pages option.' ),
                ],
                [
                    'type'        => 'radio',
                    'id'          => 'devm_test_readio_for_repeater_popup',
                    'label'       => __( 'Custom Radio Selection' ),
                    'description' => __( 'This is a custom radio input.' ),
                    'choices'     => [
                        'red'   => __( 'Red' ),
                        'blue'  => __( 'Blue' ),
                        'green' => __( 'Green' ),
                    ],
                ],

            ],
        ] );

        $this->add_tab( [
            'id'      => 'first_tab',
            'section' => 'devmonsta_text_settings_section',
            'tabs'    => [
                [
                    'id'       => 'tab_1',
                    'label'    => 'Tab 1',
                    'controls' => [
                        'control_id_1',
                        'control_id_2',
                        'control_id_3',
                    ],
                ],
            ],
        ] );

        $control_1 = [
            'id'      => 'devm_media',
            'type'    => 'media',
            'section' => 'devmonsta_text_settings_section',
            'label'   => __( 'Media' ),
        ];

        $control_2 = [
            'id'      => 'devm_media',
            'type'    => 'media',
            'section' => 'devmonsta_text_settings_section',
            'label'   => __( 'Media' ),
        ];

        $this->add_tab( [
            'id'      => 'first_tab',
            'section' => 'devmonsta_text_settings_section',
            'tabs'    => [
                [
                    'id'       => 'tab_1',
                    'label'    => 'Tab 1',
                    'controls' => [$control_1, $control_2],
                ],
            ],
        ] );


        // format: devm_theme_option($option_name)
        // devm_print(devm_theme_option('devm_date_time'));
    }

}
