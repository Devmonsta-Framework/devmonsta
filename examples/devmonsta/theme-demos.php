<?php

function devm_import_files_theme() {
    return [
        [
            'id'                       => '1',
            'import_title'             => 'Demo (1 - 2)',
            'import_file_url'          => 'http://demo.themewinter.com/wp/demo-content/privsa/main/privsa-main.xml',
            'import_desc'              => 'Here goes the description of this demo 1 to 2',
            'import_preview_image_url' => 'http://demo.themewinter.com/wp/demo-content/privsa/main/screenshot.png',
            'required_plugin'          => ["elementor", "elementskit-lite", "metform"],
        ],
        [
            'id'                       => '2',
            'import_title'             => 'One Page ',
            'import_file_url'          => 'http://demo.themewinter.com/wp/demo-content/privsa/onepage/onepage.xml',
            'import_desc'              => 'Here goes the description of this demo 1 to 2',
            'import_preview_image_url' => 'http://demo.themewinter.com/wp/demo-content/privsa/onepage/screenshot.png',
            'required_plugin'          => ["elementor", "elementskit-lite", "metform"],
        ]
    ];
}
add_filter( 'devm_import_demo_files', 'devm_import_files_theme' );
