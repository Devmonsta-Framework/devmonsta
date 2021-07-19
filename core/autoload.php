<?php

if ( !defined( 'DEVM' ) ) {
    die( 'Forbidden' );
}

add_filter( 'upload_mimes', 'my_myme_types', 1, 1 );
function my_myme_types( $mime_types ) {
    $mime_types['svg']  = 'image/svg+xml';    // Adding .svg extension
    $mime_types['json'] = 'application/json'; // Adding .json extension

    unset( $mime_types['xls'] );  // Remove .xls extension
    unset( $mime_types['xlsx'] ); // Remove .xlsx extension

    return $mime_types;
}