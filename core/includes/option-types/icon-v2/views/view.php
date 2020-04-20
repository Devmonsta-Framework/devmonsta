<?php

if (! defined('DMS')) { die('Forbidden'); }

/*
echo 'ID';
dms_print($id);
echo 'OPTION';
dms_print($option);
echo 'DATA';
dms_print($data);
echo 'JSON';
dms_print($json);
 */

$wrapper_attr = array(
	'class' => $option['attr']['class'] . ' dms-icon-v2-preview-' . $option['preview_size'],
	'id' => $option['attr']['id'],
	'data-dms-modal-size' => $option['popup_size']
);

unset($option['attr']['class'], $option['attr']['id']);

?>

<div <?php echo dms_attr_to_html($wrapper_attr) ?>>
	<input <?php echo dms_attr_to_html($option['attr']) ?> type="hidden" />
</div>

