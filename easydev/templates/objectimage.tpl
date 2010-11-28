<?php
/*********************************************************************************
 * Autogenerated script
 * EasyDev 1.x copyright Patrick Mingard 2007-2010
 * Any modification of this code may alter the behaviour of EasyDev 2.x console
 ********************************************************************************/

require_once('../includes/connection.php'); // includes the connection to the database
require_once('object_<% echo $this->name; %>.class.php');

if(isset($_GET['id']) && ($object = <% echo $this->name; %>::findByPrimaryId($_GET['id'])) && $object-><% echo $fieldname; %>){
	$paddedIdentifier = str_pad($object->id, 9, '0', STR_PAD_LEFT);
	$d1 = substr($paddedIdentifier, 0, 3);
	$d2 = substr($paddedIdentifier, 3, 3);
	$basedirectory = 'resources/<% echo $this->name; %>/<% echo $fieldname; %>/'.$d1.'/'.$d2.'/';
	if(file_exists('../'.$basedirectory)){ // first requirement, the directory for the image should exists, otherwise there is a major problem
		$nativedirectory = $basedirectory.'native/'; // directory with the original file
		// if the user wants the image in a specific width, and this width satisfies some requirements
		if(isset($_GET['width']) && is_numeric($_GET['width']) && $_GET['width'] > 0 && $_GET['width'] < MAX_IMAGE_WIDTH){
			$destinationdirectory = $basedirectory.$_GET['width'].'/'; // the directory where the files will go after resize
			if(!file_exists('../'.$destinationdirectory)){ // if the destination directory does not exists
				$dir = dir('../'.$basedirectory); // open the basedirectory
				$i = 0;
				// count the number of directory already present (directories for other image width)
				while (false !== ($entry = $dir->read())) {
					$i++;
				}
				// if the number of directories exceed the number specified in the configuration, exit the script
				if($i >= MAX_IMAGE_RESIZE_WIDTH + 2) exit(); // +2 is because the read() function will also return the "." and ".." directories.
				mkdir('../'.$destinationdirectory, 770, true); // create the directory needed for the new width
			}
			// now destinationdirectory must exist
			if(!file_exists('../'.$destinationdirectory.$object-><% echo $fieldname; %>)){ // if the image file do not exist
				// resize the file from the nativedirectory and save it to destination directory
				$imageinfos = getimagesize('../'.$nativedirectory.$object-><% echo $fieldname; %>);
				$resource = null;
				$w = $imageinfos[0];
				$h = $imageinfos[1];
				switch($imageinfos[2]){
					case IMAGETYPE_JPEG:
						$resource = imagecreatefromjpeg('../'.$nativedirectory.$object-><% echo $fieldname; %>);
						break;
					case IMAGETYPE_PNG:
						$resource = imagecreatefrompng('../'.$nativedirectory.$object-><% echo $fieldname; %>);
						break;
					case IMAGETYPE_GIF:
						$resource = imagecreatefromgif('../'.$nativedirectory.$object-><% echo $fieldname; %>);
						break;
					default:
						die();
						break;
				}
				$newwidth = $_GET['width'];
				$newheight = $newwidth * $h / $w;
				if($resource){
					$resizedresource = imagecreatetruecolor($newwidth, $newheight);
					$status = imagecopyresized($resizedresource, $resource, 0, 0, 0, 0, $newwidth, $newheight, $w, $h);
					if($status){
						switch($imageinfos[2]){
							case IMAGETYPE_GIF:
								imagegif($resizedresource, '../'.$destinationdirectory.$object-><% echo $fieldname; %>);
								break;
							case IMAGETYPE_JPEG:
								imagejpeg($resizedresource, '../'.$destinationdirectory.$object-><% echo $fieldname; %>, 95);
								break;
							case IMAGETYPE_PNG:
								imagepng($resizedresource, '../'.$destinationdirectory.$object-><% echo $fieldname; %>, 2);
								break;
						}
					} else die();
				} else die();
			}
			// redirect to the destination file
			header('Location: '.CONSOLE_PATH.$destinationdirectory.$object-><% echo $fieldname; %>);
		}
		else{ // $_GET['width'] is not specified
			// redirect to the native file
			header('Location: '.CONSOLE_PATH.$nativedirectory.$object-><% echo $fieldname; %>);
		}
	}
}

?>

