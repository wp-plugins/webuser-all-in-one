<div class='wrap'>
	<h2>WebUser All-in-One Opties Menu</h2>
	<form method='post' action='<?php $_SERVER['REQUEST_URI']; ?>'>
		<?php
			settings_fields('webuser_options');
			do_settings_sections('webuser_options');
			
			$column_list = array(
									'Standaard rechten',
									'Admin rechten',
									'Thema rechten',
									'Gravity forms rechten',
									'Gebruiker rollen rechten',
									'Diverse rechten'
								);
			
			$permission_list = array(
									  // default permissions
									  'edit_themes',
									  'edit_plugins',
									  'install_plugins',
									  'activate_plugins',
									  'list_users',
									  'switch_themes',

									  'split',
									  // admin permissions
									  'add_users',
									  'create_users',
									  'delete_plugins',
									  'delete_themes',
									  'delete_users',
									  
									  'split',
									  //'edit_theme_options',
									  'edit_users',
									  'export',
									  'import',
									  'install_themes',
									  'manage_options',
									  'promote_users',
									  'remove_users',
									  'unfiltered_upload',
									  'update_core',
									  'update_plugins',
									  'update_themes',
									  'edit_dashboard',
									  
									  'split',
									  // Gravity forms permissions
									  'gravityforms_view_settings',
									  'gravityforms_edit_settings',
									  'gravityforms_create_form',
									  
									  'split',
									  // Members roles manager permissions
									  'create_roles',
									  'delete_roles',
									  'edit_roles',
									  'list_roles',

									  'split',
									  //last permission
									  'edit_files'
									  
								);
			if (isset($_POST['save'])) {
				$userid = $_POST['user'];
				$temp_string;
				foreach($permission_list as $value) {
					if ($value !== 'split') {
						$data = '0';
						if (isset($_POST[$value]))
							$data = $_POST[$value];
						$temp_string .= ',' . $data;
					}
				}
				$temp_string = substr($temp_string, 1);
				update_site_option('webuser_' . $userid, $temp_string);
				
				echo ("<div id='message' class='updated'><p>De rechten van de gebruiker zijn aangepast!</p></div>");
			} 
			
			if (!isset($_POST['user'])) {
				$admins = get_super_admins();
				
				echo ("<h3>Rechten beheren van gebruikers:</h3>");
				echo ("<select name='user' style='width: 300px;'>");
				
				foreach($admins as $value) {
					$user = get_userdatabylogin($value);
					
					if ($user->user_login !== "admin" && $user->user_login !== "teamwork@webuser.nl")
						echo "<option value='" . $user->ID . "'>" . $user->user_login . "</options>";
				}
				echo ("</select>");
				submit_button('Selecteer gebruiker');
			} else {
				$userid = $_POST['user'];
				
				$data = get_site_option("webuser_" . $userid);
				if (!$data) {
					//user doesn't exist, create him
					echo ("Gebruiker met ID: " . $userid . " bestaat nog niet. Data word aangemaakt.");
					$temp_string;
					foreach($permission_list as $value) {
						if ($value !== 'split') {
							$temp_string .= ',0';
						}
					}
					$temp_string = substr($temp_string, 1);
					add_site_option("webuser_" . $userid, $temp_string);
					$data = $temp_string;
				}
				
				$user_data = get_userdata($userid);
				
				$perm_data = explode(',', $data);
				
				echo ("<h2>Rechten van gebruiker: " . $user_data->user_login . "</h2><p>");
				$i = 0;
				$t = 0;
				$t2 = 0;
				echo ("<input type='hidden' name='save' value='1' />
				<input type='hidden' name='user' value='" . $userid . "' />
				
				<table border='0'>
					<tr>
						<td width='300' style='vertical-align: top;'>
							<h3>" . $column_list[$t2] . "</h3>");
							$t2++;
				foreach($permission_list as $value) {
					if ($value == 'split') {
						if ($t < 2) {
							echo ("</td>
							<td width='300' style='vertical-align: top;'>
							<h3>" . $column_list[$t2] . "</h3>");
							$t++;
							
						} else {
							echo ("</td>
							</tr>
							<tr>
								<td width='300' style='vertical-align: top;'>
								<h3>" . $column_list[$t2] . "</h3>");
							$t = 0;
						}
						$t2++;
					} else {
						
						if ($perm_data[$i] == 1) {
							echo "<input type='checkbox' name='" . $value . "' value='1' checked> " . str_replace("_", " ", $value) . "<br />";
						} else {
							echo "<input type='checkbox' name='" . $value . "' value='1'> " . str_replace("_", " ", $value) . "<br />";
						}
						$i++;
					}
					
				}
				
				echo("</td>
				</tr>
				</table></p>");
				submit_button('Aanpassen');
			}
		?>
	</form>
	
</div>