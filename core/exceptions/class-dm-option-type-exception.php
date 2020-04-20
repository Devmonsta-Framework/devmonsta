<?php if ( ! defined( 'DM' ) ) {
	die( 'Forbidden' );
}


/**
 * Class DM_Option_Type_Exception
 *
 * @since 2.6.11
 */
class DM_Option_Type_Exception extends Exception {

}

/**
 * Class DM_Option_Type_Exception_Not_Found
 *
 * @since 2.6.11
 */
class DM_Option_Type_Exception_Not_Found extends DM_Option_Type_Exception {

}

/**
 * Class DM_Option_Type_Exception_Invalid_Class
 *
 * @since 2.6.11
 */
class DM_Option_Type_Exception_Invalid_Class extends DM_Option_Type_Exception {

}

/**
 * Class DM_Option_Type_Exception_Already_Registered
 *
 * @since 2.6.11
 */
class DM_Option_Type_Exception_Already_Registered extends DM_Option_Type_Exception {

}