<?php
function devm_export_timetable_plugin_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . "mp_timetable_data";
    $active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
    $plugin_to_check = 'mp-timetable/mp-timetable.php';

    if ( in_array( $plugin_to_check, $active_plugins ) ) {
        try {
            $time_slots = $wpdb->get_results("SELECT * FROM $table_name WHERE 1");
                    if (!empty($time_slots)) {
                        foreach ($time_slots as $time_slot) { 
                            ?>
                            <timeslot>
                                <column><?php echo devm_cdata($time_slot->column_id); ?></column>
                                <event><?php echo devm_cdata($time_slot->event_id); ?></event>
                                <event_start><?php echo devm_cdata($time_slot->event_start); ?></event_start>
                                <event_end><?php echo devm_cdata($time_slot->event_end); ?></event_end>
                                <user_id><?php echo devm_cdata($time_slot->user_id); ?></user_id>
                                <description><?php echo devm_cdata($time_slot->description); ?></description>
                            </timeslot>
                            <?php 
                        }
                    }
        } catch ( Exception $e ) {
            trigger_error( 'Caught exception timatable plugin: ' . $e->getMessage() );
        }
    }
}

	/**
	 * Wrap given string in XML CDATA tag.
	 *
	 * @since 1.0.0
	 *
	 * @param string $str String to wrap in XML CDATA tag.
	 *
	 * @return string
	 */
	function devm_cdata( $str ) {
		if (!seems_utf8($str)) {
			$str = utf8_encode($str);
		}
		$str = '<![CDATA[' . str_replace(']]>', ']]]]><![CDATA[>', $str) . ']]>';

		return $str;
    }
    

add_action( "rss2_head", "devm_export_timetable_plugin_data" );