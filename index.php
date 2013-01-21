<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?PHP
	error_reporting(0);

	$dir_to_browse = "./";
	//Hide Folder Name
	$exclude = array('.','..','.ftpquota','.htaccess', '.htpasswd', 'folder.gif');

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
		<table width="100%" cellpadding="20" cellspacing="5">
			<tr>
				<td bgcolor="#FFBBBD" valign="middle">'.$message.'</td>
			</tr>
		</table>';
	}
	
	$url_folder = base64_decode(trim($_GET['folder']));
	if(!empty($_GET['folder']))
		$dir_to_browse .= $url_folder."/";
?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;">

<title>Utility</title>

<style type="text/css">
body,td,th {font-family: Tahoma, Verdana; font-size:15px;}
a:link {text-decoration: none;color: #000000;}
a:visited {text-decoration: none;color: #000000;}
a:hover {text-decoration: underline;color: #000000;}
a:active {text-decoration: none;color: #000000;}
a.sort:link{text-decoration: underline;color: #FFFFFF;}
a.sort:visited{text-decoration: underline;color: #FFFFFF;}
a.sort:hover{text-decoration: underline;color: #FFFFFF;}
a.sort:active{text-decoration: none;color: #FFFFFF;}
.top_row {color: #FFFFFF;font-weight: bold;background-color: #000000;}
.table_border {border: 1px dashed #666666;}
.error {border-top-width: 2px;border-bottom-width: 2px;border-top-style: solid;border-bottom-style: solid;border-top-color: #FF666A;border-bottom-color: #FF666A;}
</style>

</head>

<body>
<p>
<?PHP
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td>';
	$this_file_name = basename($_SERVER['PHP_SELF']);
	$this_file_size = filesize($this_file_name);
	echo '&nbsp;<a href="'.$this_file_name.'">home</a>/';
	if(!empty($url_folder))
	{
		$folders_in_url = explode("/", $url_folder);
		$folders_in_url_count = count($folders_in_url);
		for($i=0;$i<$folders_in_url_count;$i++)
		{
			$temp = "";
			for($j=0;$j<$i+1;$j++)
			{
				$temp .= "/".$folders_in_url[$j];
			}
			$temp = substr($temp, 1);
			echo '<a href="'.$this_file_name.'?folder='.base64_encode($temp).'">'.$folders_in_url[$i].'</a>/';
		}
	}

	if($case_sensative_ext == 0)
		foreach($exclude_ext as $key => $val)
			$exclude_ext[$key] = strtolower($val);
	
	$folders = array();
	$files = array();
	
	$dir_content = get_dir_content($dir_to_browse);
	
	$folders['name'] = $dir_content['folders']['name'];
	$folders['date'] = $dir_content['folders']['date'];
	$folders['link'] = $dir_content['folders']['link'];
	$files['name'] = $dir_content['files']['name'];
	$files['size'] = $dir_content['files']['size'];
	$files['date'] = $dir_content['files']['date'];
	$files['link'] = $dir_content['files']['link'];
	$images_detected = $dir_content['images_detected'];
	$media_detected = $dir_content['media_detected'];
	
	
	if(!empty($folders['name']))
	{
		natcasesort($folders['name']);
		$folders_sorted = $folders['name'];			
	}
	else
		$folders_sorted = array();
		
	if(!empty($files['name']))
	{
		natcasesort($files['name']);
		$files_sorted = $files['name'];
	}
	else
		$files_sorted = array();

?>
<br><br>
<table width="100%" border="0" cellspacing="5" cellpadding="5">
    <tr>
    	<td width="60%" class="top_row">Name</td>
    	<td width="20%" class="top_row">Size</td>
    	<td width="20%" class="top_row">Date</td>
  </tr>
</table>
<?php
	if(!empty($url_folder))
	{
		$folders_in_url = explode("/", $url_folder);
		$folders_in_url_count = count($folders_in_url);
			
		$temp = "";
		for($j=0;$j<$folders_in_url_count-1;$j++)
			$temp .= "/".$folders_in_url[$j];
		$temp = substr($temp, 1);
		echo '
		<table width="100%" cellpadding="5" cellspacing="5">
			<tr>
				<td bgcolor="#DDDDDD" valign="middle"><a href="'.$this_file_name.'?folder='.base64_encode($temp).'">ㄴ Upper directory</a></td>
			</tr>
		</table>';
		
	}
?>
<?PHP 
	echo '<table width="100%" border="0" cellspacing="5" cellpadding="5">';
	$count = 0;
	foreach($folders_sorted as $key => $val)
	{
		echo 
			'<tr class="folder_bg">
				<td width="60%">
					<img src="folder.gif">
					<a href="'.$this_file_name.'?folder='.base64_encode($folders['link'][$key]).'">'.$folders['name'][$key].'</a></div>
				</td>
				<td width="20%">';
		
		if($listing_mode == 0 && $show_folder_size_http == 1)
			echo letter_size($folders['size'][$key]);
		elseif($listing_mode == 1 && $show_folder_size_ftp == 1)
			echo letter_size($folders['size'][$key]);
		else
			echo '-';
		
		echo 
				'</td>
				<td width="20%">'
					.$folders['date'][$key].
				'</td>
			</tr>';
		$count++;
	}
	$count = 0;
	foreach($files_sorted as $key => $val)
	{
		if($count%2 == 0) $file_class = "file_bg1"; else $file_class = "file_bg2";
			echo '<tr class="'.$file_class.'">
		<td width="60%">';
			
		$file_link = $files['link'][$key];
			
		echo ' <a href="'.$file_link.'">'.$files['name'][$key].'</a></div>';
	
		echo '</td>';
			
		echo '<td width="20%">'.letter_size($files['size'][$key]).'</td>
		<td width="20%">'.$files['date'][$key].'</td></tr>';
		$count++;
		echo '';

	}
	echo '</table>';
	
	if(empty($folders['name']) && empty($files['name'])) 
		echo display_error_message('No files or folders in this directory: <span class="path_font"><b>'.$url_folder.'</b></span>');
?>

</body>
</html>