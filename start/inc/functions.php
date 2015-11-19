<?php
require_once("database.php");
require_once("config.php");

$p = "categories";
if(isset($_GET)) {
	foreach($_GET as $key => $value) {
		$value = trim($value);
		if($value != "") {
			${$key} = $value;
		}
	}
}

function escape($value) {
	if (PHP_VERSION < 6) {
	  $value = get_magic_quotes_gpc() ? stripslashes($value) : $value;
	}  
	if (function_exists("mysql_real_escape_string")) {
		$value = mysql_real_escape_string($value);
	} else {
		$value = mysql_escape_string($value);
	}
	return $value;
}

function getSize($case,$img) {
	$file = getimagesize($img);
	switch($case) {
		case 1:
		return $file[0];
		break;
		case 2:
		return $file[1];
		break;
		case 3:
		return $file[2];
		break;
		case 4:
		return $file[3];
		break;
	}
}

function convert_size($size) {
	if($size < 1024) {
		return "{$size} bytes";
	} elseif($size < 1048576) {
		$size_kb = round($size/1024,1);
		return "{$size_kb} KB";
	} else {
		$size_mb = round($size/1048576,1);
		return "{$size_mb} MB";
	}
}

function file_upload_error_message($error_code) {
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
        case UPLOAD_ERR_FORM_SIZE:
            return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
        case UPLOAD_ERR_PARTIAL:
            return 'The uploaded file was only partially uploaded';
        case UPLOAD_ERR_NO_FILE:
            return 'Please select the file';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing a temporary folder';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk';
        case UPLOAD_ERR_EXTENSION:
            return 'File upload stopped by extension';
        default:
            return 'Unknown upload error';
    }
}

function print_array($array="") {
	if(!empty($array)) {
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	}
}

function clean_string($phrase) {	
    $result = strtolower($phrase);
    $result = preg_replace("/[^a-z0-9\s_]/", "", $result);
    $result = trim(preg_replace("/\s+/", "_", $result));
    $result = trim(substr($result, 0, 150));
    $result = preg_replace("/\s/", "_", $result);
    return $result;
}

// IMAGE MAGIC FUNCTION
function convert_image($from, $to, $job, $new_w, $new_h='') {
	if (file_exists($from)) {
		$convert_path = 'convert';
		$commands = '';
		$imginfo = getimagesize($from);
		$orig_w = $imginfo[0];
		$orig_h = $imginfo[1];
			switch ($job) {
				case 1: // resize
					if ($orig_w > $orig_h) {						
						$commands .= ' -resize "'.$new_w.'"';						
					} else {						
						$commands .= ' -resize "x'.$new_w.'"';						
					}					
				break;
					
				case 2: // resize and crop
					if ($orig_w/$orig_h > $new_w/$new_h) {
						$commands .= ' -resize "x'.$new_h.'"';
						$resized_w = ($new_h/$orig_h) * $orig_w;
						$commands .= ' -crop "'.$new_w.'x'.$new_h.'+'.round(($resized_w - $new_w)/2).'+0"';
					} else {
						$commands .= ' -resize "'.$new_w.'"';
						$resized_h = ($new_w/$orig_w) * $orig_h;
						$commands .= ' -crop "'.$new_w.'x'.$new_h.'+0+'.round(($resized_h - $new_h)/2).'"';
					}
				break;
			}
						
			$convert = $convert_path.' '.$from.' '.$commands.' '.$to;
			exec($convert);
	}
}
?>