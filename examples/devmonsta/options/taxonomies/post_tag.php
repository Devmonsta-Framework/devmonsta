<?php

use Devmonsta\Libs\Taxonomies;

class Post_Tag extends Taxonomies {

    public function register_controls() {

        /**
         * control for text input
         */
        $this->add_control( [
            'type'  => 'text',
            'name'  => 'f_name',
            'value' => 'default text value',
            'desc'  => 'use this area to input text',
            'label' => __( 'First name', '{domain}' ),
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'text_less',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition Text Equal', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'f_name',
                    'operator'     => '==',
                    'value'        => "abc",
                ],
            ],
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'text_less',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition Text Less', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'f_name',
                    'operator'     => '<',
                    'value'        => 0,
                ],
            ],
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'text_less',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition Text Greater', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'f_name',
                    'operator'     => '>',
                    'value'        => 0,
                ],
            ],
        ] );

        /**
         * control for url input
         */
        $this->add_control( [
            'name'  => 'user_url',
            'type'  => 'url',
            'value' => 'http://www.xs.com',
            'label' => __( 'Enter valid URL', '{domain}' ),
            'desc'  => __( 'Url Description', '{domain}' ),
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'url_equal',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition url Equal', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'user_url',
                    'operator'     => '==',
                    'value'        => "abc",
                ],
            ],
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'url_greater',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition url Greater', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'user_url',
                    'operator'     => '>',
                    'value'        => 0,
                ],
            ],
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'url_less',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Less', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'user_url',
                    'operator'     => '<',
                    'value'        => 0,
                ],
            ],
        ] );


        /**
         * control for radio input
         */
        $this->add_control( [
            'type'    => 'radio',
            'name'    => 'gender',
            'desc'    => 'use this area to input radio',
            'label'   => __( 'Gender', '{domain}' ),
            'value'   => 'choice-3',
            'choices' => [
                'choice-1' => __( 'Male', '{domain}' ),
                'choice-2' => __( 'Female', '{domain}' ),
                'choice-3' => __( 'Others', '{domain}' ),
            ],
            'inline'  => false,
        ] );
        $this->add_control( [
            'box_id'     => 'post_box_2',
            'type'       => 'text',
            'name'       => 'oembed_equal',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Radio Condition on Equal', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'gender',
                    'operator'     => '==',
                    'value'        => "choice-2",
                ],
                [
                    'control_name' => 'gender',
                    'operator'     => '==',
                    'value'        => "choice-3",
                ],
            ],
        ] );

        /**
         * control for textarea
         */
        $this->add_control( [
            'type'  => 'textarea',
            'name'  => 'txt_area',
            'label' => __( 'Text Area', '{domain}' ),
            'desc'  => 'use this area to input large text',
            'value' => 'default text area value',
            'attr'  => ['class' => 'custom-class', 'data-foo' => 'bar'],
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'oembed_equal',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Equal', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'txt_area',
                    'operator'     => '==',
                    'value'        => "abc",
                ],
            ],
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'oembed_greater',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Greater', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'txt_area',
                    'operator'     => '>',
                    'value'        => 0,
                ],
            ],
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'oembed_less',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Less', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'txt_area',
                    'operator'     => '<',
                    'value'        => 0,
                ],
                [
                    'control_name' => 'txt_area',
                    'operator'     => '>',
                    'value'        => 300,
                ],
            ],
        ] );

        /**
         * control for checkbox input
         */
        $this->add_control( [
            'type'  => 'checkbox',
            'name'  => 'graduate',
            'value' => "true", // checked/unchecked
            'label' => __( 'Graduated', '{domain}' ),
            'desc'  => __( "check if you're graduated", '{domain}' ),
            'text'  => __( 'Yes', '{domain}' ),
        ] );
        $this->add_control( [
            'box_id'     => 'post_box_2',
            'type'       => 'text',
            'name'       => 'oembed_equal',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Equal Checkbox', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'graduate',
                    'operator'     => '==',
                    'value'        => true,
                ],
            ],
        ] );

        /**
         * control for color-picker input
         */
        $this->add_control( [
            'name'     => 'color',
            'type'     => 'color-picker',
            'label'    => __( 'Wp Color Picker Example', '{domain}' ),
            'desc'     => __( 'description of color-picker goes here', '{domain}' ),
            'value'    => '#FF0000',
            'palettes' => ['#ba4e4e', '#0ce9ed', '#941940'],
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'oembed_equal',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Equal Color Picker', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'color',
                    'operator'     => '==',
                    'value'        => "#ffffff",
                ],
                [
                    'control_name' => 'color',
                    'operator'     => '==',
                    'value'        => "#0a0000",
                ],
            ],
        ] );


        /**
         * control for multiple-checkbox input
         */
        $this->add_control( [
            'name'    => 'multiple_checkboxes',
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
        $this->add_control( [
            'box_id'     => 'post_box_2',
            'type'       => 'text',
            'name'       => 'multi_equal',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Equal Multi-checkbox', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'multiple_checkboxes[]',
                    'operator'     => '==',
                    'value'        => "choice-3",
                ],
                [
                    'control_name' => 'multiple_checkboxes[]',
                    'operator'     => '==',
                    'value'        => "choice-2",
                ],
            ],
        ] );

        /**
         * control for hidden input
         */
        $this->add_control( [
            'type'  => 'hidden',
            'value' => 'defaultvalue',
            'name'  => 'hidden_field',
            'attr'  => ['class' => 'custom-class', 'data-foo' => 'bar'],
        ] );

        /**
         * control for dropdown select
         */
        $this->add_control( [
            'name'    => 'select',
            'type'    => 'select',
            'value'   => 'choice-3',
            'label'   => __( 'Select Single', '{domain}' ),
            'desc'    => __( 'select description goes here', '{domain}' ),
            'choices' => [
                ''         => '---',
                'choice-1' => __( 'Choice One', '{domain}' ),
                'choice-2' => __( 'Choice Two', '{domain}' ),
                'choice-3' => __( 'Choice Three', '{domain}' ),
            ],
        ] );
        $this->add_control( [
            'box_id'     => 'post_box_2',
            'type'       => 'text',
            'name'       => 'oembed_equal',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Equal Select', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'select',
                    'operator'     => '==',
                    'value'        => "choice-3",
                ],
                [
                    'control_name' => 'select',
                    'operator'     => '==',
                    'value'        => "choice-2",
                ],
            ],
        ] );

        /**
         * control for multiple select
         */
        $this->add_control( [
            'name'    => 'select_multiple',
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
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'oembed_equal',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on multiselect', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'select_multiple[]',
                    'operator'     => '==',
                    'value'        => "choice-3",
                ],
                [
                    'control_name' => 'select_multiple[]',
                    'operator'     => '==',
                    'value'        => "choice-2",
                ],
            ],
        ] );

        /**
         * control for upload input
         */
        $this->add_control( [
            'name'     => 'upload_image',
            'type'     => 'upload',
            'label'    => __( 'Upload Image', '{domain}' ),
            'desc'     => __( 'Description', '{domain}' ),
            'multiple' => true,
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'upload_image',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Upload', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'upload_image',
                    'operator'     => '==',
                    'value'        => 36,
                ]
            ],
        ] );

        /**
         * control for slider input
         */
        $this->add_control( [
            'name'       => 'slider_widget',
            'label'      => __( 'Wp Slider Example', '{domain}' ),
            'desc'       => __( 'description of slider goes here', '{domain}' ),
            'type'       => 'slider',
            'value'      => 33,
            'properties' => [
                'min'  => 0,
                'max'  => 100,
                'step' => 1,
            ],
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'url_equal',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Slider Equal', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'slider_widget',
                    'operator'     => '==',
                    'value'        => 50,
                ],
            ],
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'url_greater',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Slider Greater', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'slider_widget',
                    'operator'     => '>',
                    'value'        => 50,
                ],
            ],
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'url_less',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Slider Less', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'slider_widget',
                    'operator'     => '<',
                    'value'        => 50,
                ],
            ],
        ] );

        /**
         * control for range-slider input
         */
        $this->add_control( [
            'name'       => 'range_slider_widget',
            'type'       => 'range-slider',
            'label'      => __( 'Wp Range Slider Example', '{domain}' ),
            'desc'       => __( 'description of range slider goes here', '{domain}' ),
            'value'      => [
                'from' => 10,
                'to'   => 33,
            ],
            'properties' => [
                'min'  => 0,
                'max'  => 100,
                'step' => 1,
            ],
        ] );

        /**
         * Control for Html input
         */
        $this->add_control( [
            'label' => __( 'Html Input', '{domain}' ),
            'desc'  => __( 'html description goes here', '{domain}' ),
            'type'  => 'html',
            'name'  => 'html',
            'value' => 'My <b>custom</b> <em>HTML</em> <i>Italic<i> <p>Paragraph</p>',
        ] );

        /**
         * control for date-picker input
         */
        $this->add_control( [
            'type'         => 'date-picker',
            'name'         => 'start_date',
            'value'        => '10-05-2020',
            'label'        => __( 'Date Picker', '{domain}' ),
            'desc'         => __( 'date picker description goes here', '{domain}' ),
            'monday-first' => true,         // The week will begin with Monday; for Sunday, set to false
            'min-date'     => "10-05-2020", // By default minimum date will be current day. Set a date in format Y-m-d as a start date
            'max-date'     => null,         // By default there is not maximum date. Set a date in format Y-m-d as a start date
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'url_equal',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Equal Date', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'start_date',
                    'operator'     => '==',
                    'value'        => '2020-07-10',
                ],
                [
                    'control_name' => 'start_date',
                    'operator'     => '==',
                    'value'        => '2020-07-15',
                ]
            ],
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'url_equal',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Greater Date', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'start_date',
                    'operator'     => '>',
                    'value'        => '2020-07-25',
                ],
            ],
        ] );

        /**
         * control for rgba-color-picker input
         */
        $this->add_control( [
            'name'     => 'rgba_color',
            'type'     => 'rgba-color-picker',
            'label'    => __( 'Wp RGBA Color Picker Example', '{domain}' ),
            'desc'     => __( 'description of rgba-color-picker goes here', '{domain}' ),
            'value'    => 'rgba(255,25,0,0.95)',
            'palettes' => ['#ba4e4e', '#5f9419', '#381994'],
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'url_equal',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Equal Rgba', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'rgba_color',
                    'operator'     => '==',
                    'value'        => 'rgba(220,250,255,0.95)',
                ],
            ],
        ] );

        /**
         * control for datetime-picker input
         */
        $this->add_control( [
            'type'            => 'datetime-picker',
            'name'            => 'date_time_picker',
            'value'           => '10-05-2020 12:45',
            'label'           => __( 'Date Time Picker', '{domain}' ),
            'desc'            => __( 'date time picker description', '{domain}' ),
            'datetime-picker' => [
                'date-format'  => 'Y-m-d H:i',              // Format datetime.
                'time-24'      => true,
                'min-date'    => "10-05-2020 12:00", // By default minimum date will be current day. Set a date in format Y-m-d as a start date
                'max-date'    => null,               // By default there is not maximum date. Set a date in format Y-m-d as a start date
                'timepicker'  => true,               // Show timepicker.
                'defaultTime' => '12:00',            // If the input value is empty, timepicker will set time use defaultTime.
            ],
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'url_equal',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Equal', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'date_time_picker',
                    'operator'     => '==',
                    'value'        => '2020-06-15 10:00',
                ],
                [
                    'control_name' => 'date_time_picker',
                    'operator'     => '==',
                    'value'        => '2020-06-10 12:00',
                ],
            ],
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'url_equal',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Equal', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'date_time_picker',
                    'operator'     => '>',
                    'value'        => '2020-06-25 12:00',
                ],
            ],
        ] );


        /**
         * control for datetime-range input
         */
        $this->add_control( [
            'type'            => 'datetime-range',
            'value'           => [
                'from' => '2020/05/10 12:00 am',
                'to'   => '2020/05/18 07:55 pm',
            ],
            'name'            => 'date_time_range_picker',
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
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'url_equal',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Equal Date-range', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'date_time_range_picker',
                    'operator'     => '==',
                    'value'        => '2020-06-10 12:00 - 2020-06-18 12:00',
                ],
                [
                    'control_name' => 'date_time_range_picker',
                    'operator'     => '==',
                    'value'        => '2020-06-10 12:00 to 2020-06-18 06:00',
                ]
            ],
        ] );

        /**
         * control for gradient input
         */
        $this->add_control( [
            'name'       => 'gradient',
            'type'       => 'gradient',
            'label'      => __( 'Wp Gradient Picker Example', '{domain}' ),
            'desc'       => __( 'description of gradient-picker goes here', '{domain}' ),
            'value'      => [
                'primary'   => '#F36066',
                'secondary' => '#0000FF',
            ],
            // 'conditions' => [
            //     [
            //         'control_name' => 'setting_1',
            //         'operator'     => '==',
            //         'value'        => true,

            //     ],
            //     [
            //         'control_name' => 'setting_3',
            //         'operator'     => '==',
            //         'value'        => true,
            //     ],
            // ],
        ] );

        /**
         * control for oembed input
         */
        $this->add_control( [
            'name'    => 'oembed_field',
            'type'    => 'oembed',
            'label'   => __( 'Oembed Input', '{domain}' ),
            'desc'    => __( 'Oembed text description', '{domain}' ),
            'attr'    => ['class' => 'custom-class', 'data-foo' => 'bar'],
            'value'   => 'https://www.youtube.com/watch?v=0Nh11GI4-Gc',
            'preview' => [
                'width'      => 400, // optional, if you want to set the fixed width to iframe
                'height'     => 400, // optional, if you want to set the fixed height to iframe
                'keep_ratio' => false,
            ],
        ] );

        /**
         * control for wp-editor input
         */
        $this->add_control( [
            'name'          => 'wp_editor',
            'type'          => 'wp-editor',
            'label'         => __( 'Wp Editor Example', '{domain}' ),
            'desc'          => __( 'description of wp-editor goes here', '{domain}' ),
            'editor_height' => 400,
            'wpautop'       => true,
            'editor_type'   => true, // tinymce, false: HTML
        ] );

        /**
         * control for dimension input
         */
        $this->add_control( [
            'name'  => 'padding_dimension',
            'type'  => 'dimensions',
            'label' => __( 'Dimension Input', '{domain}' ),
            'desc'  => __( 'Dimension text description', '{domain}' ),
            'attr'  => ['class' => 'custom-class', 'data-foo' => 'bar'],
            'value' => [
                'top'      => '9',
                'right'    => '8',
                'bottom'   => '4',
                'left'     => '1',
                'isLinked' => true,
            ],

        ] );

        /**
         * Control for Thumbnail Image picker Input
         */
        $this->add_control( [
            'name'    => 'i_p',
            'type'    => 'image-picker',
            'value'   => 'value-2',
            'attr'    => [
                'class'    => 'custom-class',
                'data-foo' => 'bar',
            ],
            'label'   => __( 'Thumbnail Image Picker', '{domain}' ),
            'desc'    => __( 'Description', '{domain}' ),
            'help'    => __( 'Help tip', '{domain}' ),
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
        $this->add_control( [
            'box_id'     => 'post_box_2',
            'type'       => 'text',
            'name'       => 'url_equal',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Image Picker', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'i_p',
                    'operator'     => '==',
                    'value'        => 'value-2',
                ],
                [
                    'control_name' => 'i_p',
                    'operator'     => '==',
                    'value'        => 'value-5',
                ],
            ],
        ] );

       /**
         * Control for switcher input
         */
        $this->add_control( [
            'type'         => 'switcher',
            'name'         => 'devm_switcher',
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
        $this->add_control( [
            'box_id'     => 'post_box_2',
            'type'       => 'text',
            'name'       => 'url_equal',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Equal', '{domain}' ),
            'conditions' => [
                [
                    'control_name' => 'devm_switcher',
                    'operator'     => '==',
                    'value'        => 'goodbye',
                ],
            ],
        ] );

        /**
         * Control for Typography Input
         */
        $this->add_control( [
            'name'       => 'typo',
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
                'color'          => '#0000ff',
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
         * control for icon-picker input
         */
        $this->add_control( [
            'name'  => 'icon_picker',
            'type'  => 'icon',
            'value'  => [
                'icon' => 'fas fa-at',
                'type' => 'devm-font-awesome',
            ],
            'label' => __( 'Select Icon', '{domain}' ),
            'desc'  => __( 'Select icon description', '{domain}' ),
            'attr'  => ['class' => 'custom-class', 'data-foo' => 'bar'],
        ] );
        $this->add_control( [
            'type'       => 'text',
            'name'       => 'oembed_equal',
            'value'      => 'default text value',
            'desc'       => 'use this area to input text',
            'label'      => __( 'Condition on Icon with Equal', '{domain}' ),
            'conditions' => [
                // [
                //     'control_name' => 'icon_picker',
                //     'operator'     => '==',
                //     'value'        => "fas fa-at",
                // ],
                [
                    'control_name' => 'icon_picker',
                    'operator'     => '==',
                    'value'        => "fab fa-500px",
                ],
            ],
        ] );

        // devm_print(devm_taxonomy("2", "devmonsta_user_url"));

    }

}
