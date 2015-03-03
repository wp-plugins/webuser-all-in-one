<?php
global $wpdb;
include_once('../../../../wp-load.php');
global $nggdb;
$data = esc_attr($_POST['data']);
$action = esc_attr($_POST['action']);

switch($action) {
	case 'get':
		//echo $data;
		$gallery = $nggdb->get_gallery($data);
		$images = '';
		foreach($gallery as $value) {
			if ($images === '') {
				$images .= $value->id;
				continue;
			}

			$images .= "," . $value->id;
		}

		echo $images;
	break;
	case 'call':
		$html = "<table>
				<tr style='border-bottom: 1px solid;'>
					<td width='32'>Image</td>
					<td width='200'>Filename</td>
					<td width='16'>selected</td>
				</tr>";
		$imgids = explode(',' , $data);
		for($i = 0;$i < count($imgids);$i++) {
			$name = $nggdb->find_image($imgids[$i])->filename;
			$html .= "<tr>
				<td><input type='checkbox' class='imgSelect' name='imgSelect[]' value='" . $imgids[$i] . "' checked /></td>
				<td>" . $name . "</td>
				<td> <img src='" . $nggdb->find_image($imgids[$i])->imageURL . "' width='50' height='50' /> </td>
			</tr>";
		}
		$html .= "</table>";
		$html = apply_filters('the_content', $html );
		echo $html;
	break;
}
?>