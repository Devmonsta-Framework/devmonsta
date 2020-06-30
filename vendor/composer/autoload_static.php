<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit678b69e97bc75a31559092d5ed937d64
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'Devmonsta\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Devmonsta\\' => 
        array (
            0 => __DIR__ . '/../..' . '/core',
        ),
    );

    public static $classMap = array (
        'DM_Db_Options_Model' => __DIR__ . '/../..' . '/core/helpers/class-dm-db-options-model.php',
        'DM_Db_Options_Model_Customizer' => __DIR__ . '/../..' . '/core/helpers/database.php',
        'DM_Db_Options_Model_Post' => __DIR__ . '/../..' . '/core/helpers/database.php',
        'DM_Db_Options_Model_Term' => __DIR__ . '/../..' . '/core/helpers/database.php',
        'DM_Flash_Messages' => __DIR__ . '/../..' . '/core/helpers/class-dm-flash-messages.php',
        'DM_Request' => __DIR__ . '/../..' . '/core/helpers/class-dm-request.php',
        'DM_Session' => __DIR__ . '/../..' . '/core/helpers/class-dm-session.php',
        'DM_WP_Customize_Panel' => __DIR__ . '/../..' . '/core/options/customizer/libs/sections.php',
        'DM_WP_Customize_Section' => __DIR__ . '/../..' . '/core/options/customizer/libs/sections.php',
        'DM_WP_Meta' => __DIR__ . '/../..' . '/core/helpers/class-dm-wp-meta.php',
        'Devmonsta\\Bootstrap' => __DIR__ . '/../..' . '/core/bootstrap.php',
        'Devmonsta\\Libs\\Customizer' => __DIR__ . '/../..' . '/core/libs/customizer.php',
        'Devmonsta\\Libs\\Posts' => __DIR__ . '/../..' . '/core/libs/posts.php',
        'Devmonsta\\Libs\\Repeater' => __DIR__ . '/../..' . '/core/libs/repeater.php',
        'Devmonsta\\Libs\\Taxonomies' => __DIR__ . '/../..' . '/core/libs/taxonomies.php',
        'Devmonsta\\Options\\Customizer\\Controls' => __DIR__ . '/../..' . '/core/options/customizer/controls.php',
        'Devmonsta\\Options\\Customizer\\Controls\\CheckboxMultiple\\CheckboxMultiple' => __DIR__ . '/../..' . '/core/options/customizer/controls/checkbox-multiple/checkbox-multiple.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Checkbox\\Checkbox' => __DIR__ . '/../..' . '/core/options/customizer/controls/checkbox/checkbox.php',
        'Devmonsta\\Options\\Customizer\\Controls\\ColorPicker\\ColorPicker' => __DIR__ . '/../..' . '/core/options/customizer/controls/color-picker/color-picker.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Color\\Color' => __DIR__ . '/../..' . '/core/options/customizer/controls/color/color.php',
        'Devmonsta\\Options\\Customizer\\Controls\\DatePicker\\DatePicker' => __DIR__ . '/../..' . '/core/options/customizer/controls/date-picker/date-picker.php',
        'Devmonsta\\Options\\Customizer\\Controls\\DatetimePicker\\DatetimePicker' => __DIR__ . '/../..' . '/core/options/customizer/controls/datetime-picker/datetime-picker.php',
        'Devmonsta\\Options\\Customizer\\Controls\\DatetimeRange\\DatetimeRange' => __DIR__ . '/../..' . '/core/options/customizer/controls/datetime-range/datetime-range.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Dimensions\\Dimensions' => __DIR__ . '/../..' . '/core/options/customizer/controls/dimensions/dimensions.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Gradient\\Gradient' => __DIR__ . '/../..' . '/core/options/customizer/controls/gradient/gradient.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Hidden\\Hidden' => __DIR__ . '/../..' . '/core/options/customizer/controls/hidden/hidden.php',
        'Devmonsta\\Options\\Customizer\\Controls\\HtmlEditor\\HtmlEditor' => __DIR__ . '/../..' . '/core/options/customizer/controls/html-editor/html-editor.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Html\\Html' => __DIR__ . '/../..' . '/core/options/customizer/controls/html/html.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Icon\\Icon' => __DIR__ . '/../..' . '/core/options/customizer/controls/icon/icon.php',
        'Devmonsta\\Options\\Customizer\\Controls\\ImagePicker\\ImagePicker' => __DIR__ . '/../..' . '/core/options/customizer/controls/image-picker/image-picker.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Media\\Media' => __DIR__ . '/../..' . '/core/options/customizer/controls/media/media.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Multiselect\\Multiselect' => __DIR__ . '/../..' . '/core/options/customizer/controls/multiselect/multiselect.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Number\\Number' => __DIR__ . '/../..' . '/core/options/customizer/controls/number/number.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Oembed\\Oembed' => __DIR__ . '/../..' . '/core/options/customizer/controls/oembed/oembed.php',
        'Devmonsta\\Options\\Customizer\\Controls\\RangeSlider\\RangeSlider' => __DIR__ . '/../..' . '/core/options/customizer/controls/range-slider/range-slider.php',
        'Devmonsta\\Options\\Customizer\\Controls\\RgbaColorPicker\\RgbaColorPicker' => __DIR__ . '/../..' . '/core/options/customizer/controls/rgba-color-picker/rgba-color-picker.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Select\\Select' => __DIR__ . '/../..' . '/core/options/customizer/controls/select/select.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Slider\\Slider' => __DIR__ . '/../..' . '/core/options/customizer/controls/slider/slider.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Switcher\\Switcher' => __DIR__ . '/../..' . '/core/options/customizer/controls/switcher/switcher.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Text\\Text' => __DIR__ . '/../..' . '/core/options/customizer/controls/text/text.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Textarea\\Textarea' => __DIR__ . '/../..' . '/core/options/customizer/controls/textarea/textarea.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Toggle\\Toggle' => __DIR__ . '/../..' . '/core/options/customizer/controls/toggle/toggle.php',
        'Devmonsta\\Options\\Customizer\\Controls\\Typography\\Typography' => __DIR__ . '/../..' . '/core/options/customizer/controls/typography/typography.php',
        'Devmonsta\\Options\\Customizer\\Controls\\WpEditor\\WpEditor' => __DIR__ . '/../..' . '/core/options/customizer/controls/wp-editor/wp-editor.php',
        'Devmonsta\\Options\\Customizer\\Customizer' => __DIR__ . '/../..' . '/core/options/customizer/customizer.php',
        'Devmonsta\\Options\\Customizer\\Structure' => __DIR__ . '/../..' . '/core/options/customizer/structure.php',
        'Devmonsta\\Options\\Customizer\\Structures\\Control' => __DIR__ . '/../..' . '/core/options/customizer/structures/control.php',
        'Devmonsta\\Options\\Posts\\Controls\\CheckboxMultiple\\CheckboxMultiple' => __DIR__ . '/../..' . '/core/options/posts/controls/checkbox-multiple/checkbox-multiple.php',
        'Devmonsta\\Options\\Posts\\Controls\\Checkbox\\Checkbox' => __DIR__ . '/../..' . '/core/options/posts/controls/checkbox/checkbox.php',
        'Devmonsta\\Options\\Posts\\Controls\\ColorPicker\\ColorPicker' => __DIR__ . '/../..' . '/core/options/posts/controls/color-picker/color-picker.php',
        'Devmonsta\\Options\\Posts\\Controls\\DatePicker\\DatePicker' => __DIR__ . '/../..' . '/core/options/posts/controls/date-picker/date-picker.php',
        'Devmonsta\\Options\\Posts\\Controls\\DatetimePicker\\DatetimePicker' => __DIR__ . '/../..' . '/core/options/posts/controls/datetime-picker/datetime-picker.php',
        'Devmonsta\\Options\\Posts\\Controls\\DatetimeRange\\DatetimeRange' => __DIR__ . '/../..' . '/core/options/posts/controls/datetime-range/datetime-range.php',
        'Devmonsta\\Options\\Posts\\Controls\\Dimensions\\Dimensions' => __DIR__ . '/../..' . '/core/options/posts/controls/dimensions/dimensions.php',
        'Devmonsta\\Options\\Posts\\Controls\\Gradient\\Gradient' => __DIR__ . '/../..' . '/core/options/posts/controls/gradient/gradient.php',
        'Devmonsta\\Options\\Posts\\Controls\\Hidden\\Hidden' => __DIR__ . '/../..' . '/core/options/posts/controls/hidden/hidden.php',
        'Devmonsta\\Options\\Posts\\Controls\\Html\\Html' => __DIR__ . '/../..' . '/core/options/posts/controls/html/html.php',
        'Devmonsta\\Options\\Posts\\Controls\\Icon\\Icon' => __DIR__ . '/../..' . '/core/options/posts/controls/icon/icon.php',
        'Devmonsta\\Options\\Posts\\Controls\\ImagePicker\\ImagePicker' => __DIR__ . '/../..' . '/core/options/posts/controls/image-picker/image-picker.php',
        'Devmonsta\\Options\\Posts\\Controls\\Multiselect\\Multiselect' => __DIR__ . '/../..' . '/core/options/posts/controls/multiselect/multiselect.php',
        'Devmonsta\\Options\\Posts\\Controls\\Oembed\\Oembed' => __DIR__ . '/../..' . '/core/options/posts/controls/oembed/oembed.php',
        'Devmonsta\\Options\\Posts\\Controls\\Radio\\Radio' => __DIR__ . '/../..' . '/core/options/posts/controls/radio/radio.php',
        'Devmonsta\\Options\\Posts\\Controls\\RangeSlider\\RangeSlider' => __DIR__ . '/../..' . '/core/options/posts/controls/range-slider/range-slider.php',
        'Devmonsta\\Options\\Posts\\Controls\\RgbaColorPicker\\RgbaColorPicker' => __DIR__ . '/../..' . '/core/options/posts/controls/rgba-color-picker/rgba-color-picker.php',
        'Devmonsta\\Options\\Posts\\Controls\\Select\\Select' => __DIR__ . '/../..' . '/core/options/posts/controls/select/select.php',
        'Devmonsta\\Options\\Posts\\Controls\\Slider\\Slider' => __DIR__ . '/../..' . '/core/options/posts/controls/slider/slider.php',
        'Devmonsta\\Options\\Posts\\Controls\\Switcher\\Switcher' => __DIR__ . '/../..' . '/core/options/posts/controls/switcher/switcher.php',
        'Devmonsta\\Options\\Posts\\Controls\\Text\\Text' => __DIR__ . '/../..' . '/core/options/posts/controls/text/text.php',
        'Devmonsta\\Options\\Posts\\Controls\\Textarea\\Textarea' => __DIR__ . '/../..' . '/core/options/posts/controls/textarea/textarea.php',
        'Devmonsta\\Options\\Posts\\Controls\\Typography\\Typography' => __DIR__ . '/../..' . '/core/options/posts/controls/typography/typography.php',
        'Devmonsta\\Options\\Posts\\Controls\\Upload\\Upload' => __DIR__ . '/../..' . '/core/options/posts/controls/upload/upload.php',
        'Devmonsta\\Options\\Posts\\Controls\\Url\\Url' => __DIR__ . '/../..' . '/core/options/posts/controls/url/url.php',
        'Devmonsta\\Options\\Posts\\Controls\\WpEditor\\WpEditor' => __DIR__ . '/../..' . '/core/options/posts/controls/wp-editor/wp-editor.php',
        'Devmonsta\\Options\\Posts\\Posts' => __DIR__ . '/../..' . '/core/options/posts/posts.php',
        'Devmonsta\\Options\\Posts\\Structure' => __DIR__ . '/../..' . '/core/options/posts/structure.php',
        'Devmonsta\\Options\\Posts\\Validator' => __DIR__ . '/../..' . '/core/options/posts/validator.php',
        'Devmonsta\\Options\\Posts\\View' => __DIR__ . '/../..' . '/core/options/posts/view.php',
        'Devmonsta\\Options\\Taxonomies\\Controls\\ColorPicker\\ColorPicker' => __DIR__ . '/../..' . '/core/options/taxonomies/controls/color-picker/color-picker.php',
        'Devmonsta\\Options\\Taxonomies\\Structure' => __DIR__ . '/../..' . '/core/options/taxonomies/structure.php',
        'Devmonsta\\Options\\Taxonomies\\Taxonomies' => __DIR__ . '/../..' . '/core/options/taxonomies/taxonomies.php',
        'Devmonsta\\Rest' => __DIR__ . '/../..' . '/core/rest.php',
        'Devmonsta\\Traits\\Singleton' => __DIR__ . '/../..' . '/core/traits/singleton.php',
        'Dm_Cache' => __DIR__ . '/../..' . '/core/helpers/class-dm-cache.php',
        'Dm_Cache_Not_Found_Exception' => __DIR__ . '/../..' . '/core/helpers/class-dm-cache.php',
        'Dm_Callback' => __DIR__ . '/../..' . '/core/helpers/class-dm-callback.php',
        'Dm_Dumper' => __DIR__ . '/../..' . '/core/helpers/class-dm-dumper.php',
        'Dm_Resize' => __DIR__ . '/../..' . '/core/helpers/class-dm-resize.php',
        'JT_Customize_Control_Radio_Image' => __DIR__ . '/../..' . '/core/options/customizer/controls/radio/radio.php',
        'Theme_Customize_Repeater_Control' => __DIR__ . '/../..' . '/core/options/customizer/libs/customize-repeater-control.php',
        'Theme_Customize_Repeater_Popup_Control' => __DIR__ . '/../..' . '/core/options/customizer/libs/customize-repeater-control-popup.php',
        '_Dm' => __DIR__ . '/../..' . '/core/helpers/Dm.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit678b69e97bc75a31559092d5ed937d64::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit678b69e97bc75a31559092d5ed937d64::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit678b69e97bc75a31559092d5ed937d64::$classMap;

        }, null, ClassLoader::class);
    }
}
