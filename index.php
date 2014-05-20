<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?PHP
	include('main-browser-library/browser/library.php');
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;">
	
	<title>MOB LAB. Sharing</title>
	
	<script src="main-browser-library/jquery/jquery-2.1.1.min.js"></script>
	<script src="main-browser-library/bootstrap-3.1-2.1/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="main-browser-library/bootstrap-3.1-2.1/css/bootstrap.css"> 
</head>

<body style="padding:20px;">
	
	<?php
		$db_host = "localhost"; 
		$db_user = "root"; 
		$db_passwd = "";
		$db_name = "mobfileserver"; 
		$conn = mysqli_connect($db_host,$db_user,$db_passwd,$db_name);
		if (mysqli_connect_errno($conn)) {
			echo "데이터베이스 연결 실패: " . mysqli_connect_error();
		} else {
			
		}
	?>
	
	<div class="well well-sm">
		<?PHP
			$this_file_name = basename($_SERVER['PHP_SELF']);
			$this_file_size = filesize($this_file_name);
			echo '<a href="'.$this_file_name.'">home</a>/';
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
	</div>

	<table class="table table-striped">
		<thead>
			<tr>
				<th width="3%"></td>
				<th width="57%">Name</td>
				<th width="20%">Size</td>
				<th width="20%">Date</td>
			</tr>
		</thead>
		<tbody>
			<?php
				if(!empty($url_folder))
				{
					$folders_in_url = explode("/", $url_folder);
					$folders_in_url_count = count($folders_in_url);
						
					$temp = "";
					for($j=0;$j<$folders_in_url_count-1;$j++)
						$temp .= "/".$folders_in_url[$j];
					$temp = substr($temp, 1);
					echo '<tr>';
					echo '	<td valign="middle" colspan="4"><a href="'.$this_file_name.'?folder='.base64_encode($temp).'"><span class="glyphicon glyphicon-circle-arrow-left"></span>&nbsp;&nbsp;Upper directory</a></td>';
					echo '</tr>';
					
				}
			?>
			<?PHP 
				$count = 0;
				foreach($folders_sorted as $key => $val)
				{
					echo '<tr>';
					echo '	<td class="text-center"><span class="glyphicon glyphicon-folder-open"></span></td>';
					echo '	<td>';
					echo '		<a href="'.$this_file_name.'?folder='.base64_encode($folders['link'][$key]).'">'.$folders['name'][$key].'</a></div>';
					echo '	</td>';
					echo '	<td>';
					if($listing_mode == 0 && $show_folder_size_http == 1)
						echo letter_size($folders['size'][$key]);
					elseif($listing_mode == 1 && $show_folder_size_ftp == 1)
						echo letter_size($folders['size'][$key]);
					else
						echo '-';
					echo '</td>';
					echo '<td>'.$folders['date'][$key].'</td></tr>';
					$count++;
				}
				$count = 0;
				foreach($files_sorted as $key => $val)
				{
					if($count%2 == 0) $file_class = "file_bg1"; else $file_class = "file_bg2";
					
					$file_link = $files['link'][$key];
					
					echo '<tr class="'.$file_class.'">';
					echo "<td class=\"text-center\">";
					
					// icons setting
					if(strpos($files['name'][$key], '.zip') !== false || strpos($files['name'][$key], '.tar') !== false || strpos($files['name'][$key], '.gz') !== false) {
						echo "<span class=\"glyphicon glyphicon-compressed\"></span></td>";
						echo '<td><a href="'.$file_link.'" target="_blank">'.$files['name'][$key].'</a></div></td>';
					} else if(strpos($files['name'][$key], '.png') !== false || strpos($files['name'][$key], '.jpeg') !== false || strpos($files['name'][$key], '.jpg') !== false || strpos($files['name'][$key], '.bmp') !== false || strpos($files['name'][$key], '.gif') !== false) {
						echo "<span class=\"glyphicon glyphicon-picture\"></span></td>";	
						echo '<td><a href="'.$file_link.'" target="_blank">'.$files['name'][$key].'</a></div></td>';
					} else if(strpos($files['name'][$key], '.html') !== false || strpos($files['name'][$key], '.js') !== false || strpos($files['name'][$key], '.css') !== false || strpos($files['name'][$key], '.php') !== false) {
						echo "<span class=\"glyphicon glyphicon-globe\"></span></td>";	
						echo '<td><a href="'.$file_link.'" target="_blank">'.$files['name'][$key].'</a></div></td>';
					} else if(strpos($files['name'][$key], '.pdf') !== false || strpos($files['name'][$key], '.doc') !== false || strpos($files['name'][$key], '.docx') !== false || strpos($files['name'][$key], '.hwp') !== false) {
						echo "<span class=\"glyphicon glyphicon-book\"></span></td>";	
						echo '<td><a href="'.$file_link.'" target="_blank">'.$files['name'][$key].'</a></div></td>';
					} else {
						echo "</td>";
						echo '<td><a href="'.$file_link.'">'.$files['name'][$key].'</a></div></td>';
					}
					
					echo '<td>'.letter_size($files['size'][$key]).'</td>';
					echo '<td>'.$files['date'][$key].'</td></tr>';
					$count++;
			
				}
				if(empty($folders['name']) && empty($files['name'])) 
					echo display_error_message('No files or folders in this directory: <span class="path_font"><b>'.$url_folder.'</b></span>');
			?>

		</tbody>
	</table>
</body>
</html>