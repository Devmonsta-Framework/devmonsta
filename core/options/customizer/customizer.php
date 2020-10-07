<?php

namespace Devmonsta\Options\Customizer;

use Devmonsta;
use Devmonsta\Libs\Customizer as LibsCustomizer;
use Devmonsta\Options\Customizer\Panel;
use Devmonsta\Traits\Singleton;

class Customizer {

    use Singleton;

    /**
     * ==========================================
     *
     * Initial method of the customizer
     *
     * @access  public
     * @return  void
     * @since   1.0.0
     * ==========================================
     */
    public function init()
    {
        if ( !$this->check_requirements() ) {
            return;
        }

        add_action( 'customize_controls_enqueue_scripts', [$this, 'load_scripts'] );

        /**
         * Add styles and scripts
         */
        add_action( 'customize_preview_init', [$this, 'scripts_and_styles'] );

        /**
         * Get Customizer file from the
         * current active theme
         */
        $customizer_file = $this->get_customizer_file();

        /**
         * Check if the customizer file exists
         */
        if ( file_exists( $customizer_file ) )
        {
            require_once $customizer_file;
            $childrens = [];

            /**
             *================================================
             * Fetch all the class extended to Customer class
             * @Devmonsta\Libs\Customizer
             *================================================
             */

            foreach ( get_declared_classes() as $class )
            {
                if ( is_subclass_of( $class, 'Devmonsta\Libs\Customizer' ) ) {

                    /** Store all control class to @var array $childrens */
                    $childrens[] = $class;
                }

            }

            $customizer = new LibsCustomizer;

            foreach ( $childrens as $child_class )
            {
                $control = new $child_class;

                if ( method_exists( $control, 'register_controls' ) )
                {
                    $control->register_controls();
                }

            }

            /**
             * Get all panels defined in the theme
             */
            $all_panels = $customizer->all_panels();

            /**
             * Get all sections defined in the theme
             */
            $all_sections = $customizer->all_sections();

            /**
             * Get all controls defined in the theme
             */
            $all_controls = $customizer->all_controls();

            /**
             * Get all settings for the customizer defined in theme
             */
            $all_settings = $customizer->all_settings();

            /**
             * Get all tabs and the controls of the tabs
             */
            $all_tabs = $customizer->all_tabs();

            /**
             * Build the panel , sections and controls
             */
            $this->build_panels( $all_panels );
            $this->build_sections( $all_sections );
            $this->build_controls( $all_controls );
            $this->build_tabs( $all_tabs );
        }
    }

    public function check_requirements()
    {
        global $wp_customize;
        if ( isset( $wp_customize ) ) {
            // do stuff
            return true;
        }
        return false;
    }

    /**
     * ======================================
     * Get the active theme location
     * and the customzer file of the theme
     *
     * @access  public
     * @return  string
     * ======================================
     */
    public function get_customizer_file()
    {
        /**
         * Return the customizer file
         *
         * @link https://developer.wordpress.org/reference/functions/get_template_directory
         */
        return get_template_directory() . '/devmonsta/options/customizer.php';
    }

    /**
     *=================================
     * Build options for customizer
     *
     * @access  public
     * @return  void
     *=================================
     */
    public function build_controls( $args ) {

        /**
         * =====================================================
         *      Check if the @type of control is set or not
         *      Create a dynamic object of class and add the
         *      data to control
         * =====================================================
         */

        if ( !empty( $args ) )
        {
            foreach ( $args as $control )
            {
                if ( isset( $control['type'] ) )
                {
                    $type = $control['type'];
                    if ( $type == 'repeater' ) {

                        // Repeater code goes here
                        $this->build_repeater_control( $type, $control );

                    } elseif ( $type == 'addable-popup' ) {

                        // $this->build_addable_popup_control($type,$control);

                    } else {

                        /** If control type is default */
                        $this->build_control( $type, $control );

                    }

                }

            }

        }

    }

    public function build_tabs( $all_tabs )
    {
        if ( is_array( $all_tabs ) )
        {
            foreach ( $all_tabs as $tab )
            {
                $this->tab_content( $tab );
            }
        }
    }

    public function tab_content( $tab ) {

        // $tab_id = $tab['id'];

    }

    /**
     *=================================
     * Build control for customizer
     *
     * @access  public
     * @return  void
     *=================================
     */
    public function build_control( $type, $control )
    {
        if ( in_array( $type, $this->default_controls ) )
        {
            /**
             * Add Defaults controls to the customizer.
             * Defaults controls list defined on @default_controls
             */

            add_action( 'customize_register', function ( $wp_customize ) use ( $control, $type ) {
                $args             = $control;
                $id               = $args['id'];
                $args['settings'] = $id;
                $wp_customize->add_setting( $id, [
                    'default' => isset( $args['default'] ) ? $args['default'] : '',
                ] );

                if ( isset( $args['selector'] ) ) {
                    $wp_customize->selective_refresh->add_partial( $id, [
                        'selector'        => $args['selector'],
                        'render_callback' => function () use ( $args ) {
                            echo get_theme_mod( $args['settings'] );
                        },
                    ] );
                }

                if ( $type == 'media' ) {
                    $wp_customize->add_control(
                        new \WP_Customize_Media_Control(
                            $wp_customize,
                            $id,
                            $args
                        )
                    );
                } else {
                    $wp_customize->add_control(
                        new \WP_Customize_Control(
                            $wp_customize,
                            $id,
                            $args
                        )
                    );
                }
            } );

        } else {

            /**
             * Build custom controls
             */
            $this->build_custom_control( $type, $control );

        }

    }

    /**
     * ===============================
     *
     * Repeater control functionality
     *
     * @access  public
     * @return  void
     * ===============================
     *
     */
    public function build_repeater_control( $type, $control )
    {

        add_action( 'customize_register', function ( $wp_customize ) use ( $control, $type ) {
            require_once __DIR__ . '/libs/customize-repeater-control.php';

            $wp_customize->register_control_type( 'DEVM_Theme_Customize_Repeater_Control' );

            $fields = $control['fields'];

            $field_controls = [];

            foreach ( $fields as $field )
            {
                if ( in_array( $field['type'], $this->default_controls ) ) {

                    array_push( $field_controls, [
                        'key'     => $field['id'],
                        'control' => 'WP_Customize_Control',
                        'args'    => $field,
                    ] );

                } else {

                    $control_file = __DIR__ . '/controls/' . $field['type'] . '/' . $field['type'] . '.php';

                    if ( file_exists( $control_file ) ) {

                        require_once $control_file;

                        $class_name    = explode( '-', $field['type'] );
                        $class_name    = array_map( 'ucfirst', $class_name );
                        $class_name    = implode( '', $class_name );
                        $control_class = 'Devmonsta\Options\Customizer\Controls\\' . $class_name . '\\' . $class_name;

                        // Register control type

                        $wp_customize->register_control_type( $control_class );

                        if ( class_exists( $control_class ) ) {

                            array_push( $field_controls, [
                                'key'     => $field['id'],
                                'control' => $control_class,
                                'args'    => $field,
                            ] );
                        }
                    }
                }
            }

            $wp_customize->add_setting( $control['id'] , [
                'default' => '',
            ] );

            $wp_customize->add_control( new \DEVM_Theme_Customize_Repeater_Control( $wp_customize, $control['id'], [
                'label'           => esc_html__( $control['label'], 'devmonsta' ),
                'description_a'   => 'This is description',
                'section'         => $control['section'],
                'fields'          => $field_controls,
                'add_button_text' => isset( $control['add_button_text'] ) ? $control['add_button_text'] : 'Add new Item',
                'title_field'     => isset( $control['title_field'] ) ? $control['title_field'] : 'Title',
            ] ) );

        } );
    }

    public function build_addable_popup_control( $type, $control ) {

        add_action( 'customize_register', function ( $wp_customize ) use ( $control, $type ) {
            require_once __DIR__ . '/libs/customize-repeater-control-popup.php';

            $wp_customize->register_control_type( 'DEVM_Theme_Customize_Repeater_Popup_Control' );

            $fields = $control['fields'];

            $field_controls = [];

            foreach ( $fields as $field ) {

                if ( in_array( $field['type'], $this->default_controls ) ) {
                    array_push( $field_controls, [
                        'key'     => $field['id'],
                        'control' => 'WP_Customize_Control',
                        'args'    => $field,
                    ] );

                } else {

                    $control_file = __DIR__ . '/controls/' . $field['type'] . '/' . $field['type'] . '.php';

                    if ( file_exists( $control_file ) ) {

                        require_once $control_file;

                        $class_name    = explode( '-', $field['type'] );
                        $class_name    = array_map( 'ucfirst', $class_name );
                        $class_name    = implode( '', $class_name );
                        $control_class = 'Devmonsta\Options\Customizer\Controls\\' . $class_name . '\\' . $class_name;

                        $wp_customize->register_control_type( $control_class );

                        if ( class_exists( $control_class ) ) {

                            array_push( $field_controls, [
                                'key'     => $field['id'],
                                'control' => $control_class,
                                'args'    => $field,
                            ] );
                        }
                    }
                }
            }

            $wp_customize->add_setting( $control['id'], [
                'default' => '',
            ] );

            $wp_customize->add_control( new \DEVM_Theme_Customize_Repeater_Popup_Control( $wp_customize, $control['id'], [

                'label'           => esc_html__( $control['label'], 'devmonsta' ),
                'description_a'   => 'This is description',
                'section'         => $control['section'],
                'fields'          => $field_controls,
                'add_button_text' => isset( $control['add_button_text'] ) ? $control['add_button_text'] : 'Add new Item',
                'title_field'     => isset( $control['title_field'] ) ? $control['title_field'] : 'Title',

            ] ) );

        } );
    }

    /**
     * ==============================
     * Build custom control
     * for customizer
     *
     * @access  public
     * @return  void
     * ==============================
     */
    public function build_custom_control( $type, $control ) {

        $control_file = __DIR__ . '/controls/' . $type . '/' . $type . '.php';

        $class_name    = explode( '-', $type );
        $class_name    = array_map( 'ucfirst', $class_name );
        $class_name    = implode( '', $class_name );
        $control_class = 'Devmonsta\Options\Customizer\Controls\\' . $class_name . '\\' . $class_name;

        add_action( 'customize_register', function ( $wp_customize ) use ( $control_file, $control_class, $control ) {

            if ( file_exists( $control_file ) ) {

                require_once $control_file;

                if ( class_exists( $control_class ) ) {

                    $wp_customize->add_setting( $control['id'], [

                        'default' => isset( $control['default'] ) ? $control['default'] : '',

                    ] );

                    $wp_customize->add_control( new $control_class( $wp_customize, $control['id'], [

                        'section' => $control['section'],
                        $control,

                    ] ) );
                }
            }
        } );
    }

    /**
     * ==================================
     * Build Panels for customizer
     *
     * @access  public
     * @return  void
     * ==================================
     */
    public function build_panels( $panels ) {

        if ( !empty( $panels ) ) {

            include_once 'libs/sections.php';

            foreach ( $panels as $panel ) {

                add_action( 'customize_register', function ( $wp_customize ) use ( $panel ) {
                    $panel_id = $panel['id'];
                    unset( $panel['id'] );

                    $devm_panel = new \DEVM_WP_Customize_Panel( $wp_customize, $panel_id, $panel );

                    $wp_customize->add_panel( $devm_panel );
                    //$wp_customize->add_panel( $panel_id, $panel );
                } );
            }
        }
    }

    /**
     * ========================
     * Build sections in panel
     *
     * @access  public
     * @return  void
     * ========================
     */
    public function build_sections( $sections ) {

        if ( !empty( $sections ) ) {

            include_once 'libs/sections.php';

            foreach ( $sections as $section ) {

                add_action( 'customize_register', function ( $wp_customize ) use ( $section ) {

                    $section_id = $section['id'];
                    unset( $section['id'] );

                    $devm_section = new \DEVM_WP_Customize_Section( $wp_customize, $section_id, $section );

                    $wp_customize->add_section( $devm_section );

                } );

            }

        }

    }

    /**
     * ======================================
     * Repeater functionality for customizer
     *
     * @access  public
     * @return  void
     * ======================================
     */

    protected $default_controls;
    protected $control_file;

    public function __construct() {
        $this->default_controls = [
            'text',
            // 'checkbox',
            'radio',
            'select',
            'textarea',
            'dropdown-pages',
            'email',
            'url',
            // 'number',
            'hidden',
            'date',
            'media',
            // 'color',
        ];
    }

    public function scripts_and_styles() {
        wp_enqueue_script( 'devmonsta-customizer', plugin_dir_url( __FILE__ ) . 'libs/assets/js/customizer.js', ['customize-preview', 'jquery', 'jquery-ui'] );
    }

    /**
     * ===========================================
     *      Load Styles & Scripts for controls
     * ===========================================
     */
    public function load_scripts()
    {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        $colorpicker_l10n = array(
                'clear'            => __( 'Clear' ),
                'clearAriaLabel'   => __( 'Clear color' ),
                'defaultString'    => __( 'Default' ),
                'defaultAriaLabel' => __( 'Select default color' ),
                'pick'             => __( 'Select Color' ),
                'defaultLabel'     => __( 'Color value' ),
        );
        wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n ); 
        wp_enqueue_style('dm-main-style', DEVMONSTA_PATH . 'core/options/assets/css/main.css');
        wp_enqueue_style('customizer-nested-section-css', DEVMONSTA_PATH . 'core/options/customizer/libs/assets/css/customizer-nested-panel.css');
        wp_enqueue_style('customizer-css', DEVMONSTA_PATH . 'core/options/customizer/libs/assets/css/customizer.css');
        wp_enqueue_script('vue-js', DEVMONSTA_PATH . 'core/options/posts/assets/js/vue.min.js', [], null, false);
        wp_enqueue_script('dm-vue', DEVMONSTA_PATH . 'core/options/posts/assets/js/script.js', [], null, true);
        wp_enqueue_script('customizer-script', DEVMONSTA_PATH . 'core/options/customizer/libs/assets/js/script.js', [], null, true);
        wp_enqueue_script('customizer-nested-section-js',DEVMONSTA_PATH . 'core/options/customizer/libs/assets/js/customizer-nested-panel.js',[],null,true);
        wp_enqueue_script('dm-vendor-js', DEVMONSTA_PATH . 'core/options/assets/js/dm-vendor-scripts.bundle.js', ['jquery'], null, true);
        wp_enqueue_script('dm-init-js', DEVMONSTA_PATH . 'core/options/assets/js/dm-init-scripts.bundle.js', ['jquery'], null, true);
    }

}
