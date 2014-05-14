<?PHP
	error_reporting(0);

	$dir_to_browse = "./";
	//Hide Folder Name
	$exclude = array('.','..','.ftpquota','.htaccess', '.htpasswd', 'folder.gif', 'index.php.bak', 'main-browser-library');

	function get_dir_content($path)
	{
		global $ftp_stream, $url_folder, $this_file_size, $this_file_name, $case_sensative_ext, $show_folder_size_ftp, $view_mode, $exclude_ext, $exclude, $listing_mode;
		
		
		$dh  = opendir($path);
			
		while (false !== ($item = readdir($dh)))
			$content[] = $item;
			
		if(empty($content))
			return $content;
			
		$media_detected = '';
		$images_detected = '';
				
		foreach($content as $key => $val)
		{
			if(!in_array($val, $exclude))
			{
				$item_path = $path.$val;
					
				if(is_dir($item_path))
				{
					$folders['name'][] = $val;
					$folders['date'][] = date("d F Y", filectime($item_path));
					$folders['link'][] = (empty($url_folder)) ? $val : $url_folder."/".$val;
				}
				else
				{
					$file_size = filesize($item_path);
	
					if(!($val == $this_file_name && $this_file_size == $file_size))//Exclude the main index file specifically
					{
						$file_ext = strrchr($val, ".");
							
						if($case_sensative_ext == 0) $file_ext = strtolower($file_ext);
							
						if(!in_array($file_ext, $exclude_ext))
						{
							$files['name'][] = $val;
							$files['size'][] = $file_size;
							$files['link'][] = $path.rawurlencode($val);
							$files['date'][] = date("d F Y", filectime($item_path));
							if($images_detected == '')
								$images_detected = (in_array(strtolower($file_ext), array('.jpeg', '.jpg', '.png', '.gif'))) ? 1 : 0;
							if($media_detected == '')
								$media_detected = (strtolower($file_ext) == '.mp3') ? 1 : 0;
						}
					}		
				}
			}
		}
		return @array('folders' => $folders, 'files' => $files, 'images_detected' => $images_detected, 'media_detected' => $media_detected);
	}
	
	function letter_size($byte_size)
	{
		$file_size = $byte_size/1024;
		if($file_size >=  1048576)
		$file_size = sprintf("%01.2f", $file_size/1048576)." GB";
		elseif ($file_size >=  1024)
		$file_size = sprintf("%01.2f", $file_size/1024)." MB";
		else
		$file_size = sprintf("%01.1f", $file_size)." KB";
		return $file_size;
	}
	
	function display_error_message($message)
	{
		return '
			<tr>
				<td colspan="4"><div class="alert alert-danger">'.$message.'</div></td>
			</tr>';
	}
	
	$url_folder = base64_decode(trim($_GET['folder']));
	if(!empty($_GET['folder']))
		$dir_to_browse .= $url_folder."/";
?>