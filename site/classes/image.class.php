<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


/**
 * Holds the logic for image manipulation
 */
class JemImage {

	static function thumb($name,$filename,$new_w,$new_h)
	{
		# create a new instance of the class
		$image = new Zebra_Image();

		# indicate a source image (a GIF, PNG or JPEG file)
		$image->source_path = $name;

		# indicate a target image
		# note that there's no extra property to set in order to specify the target
		# image's type -simply by writing '.jpg' as extension will instruct the script
		# to create a 'jpg' file
		$image->target_path = $filename;

		# since in this example we're going to have a jpeg file, let's set the output
		# image's quality (95% has no visible effect but saves some bytes)
		$image->jpeg_quality = 95;

		# some additional properties that can be set
		# read about them in the documentation
		$image->preserve_aspect_ratio = true;
		$image->enlarge_smaller_images = false;
		$image->preserve_time = true;

		# resize the image to at best 100x100 pixels by using the "not boxed" method
		# (read more in the overview section or in the documentation)
		# and if there is an error, check what the error is about
		if (!$image->resize($new_w, $new_h, ZEBRA_IMAGE_NOT_BOXED, -1)) {

			# only admins will see these errors
			if (JFactory::getUser()->authorise('core.manage')) {

				# if there was an error, let's see what the error is about
				switch ($image->error) {
					case 1:
						echo 'Source file could not be found!';
						break;
					case 2:
						echo 'Source file is not readable!';
						break;
					case 3:
						echo 'Could not write target file!';
						break;
					case 4:
						echo 'Unsupported source file format!';
						break;
					case 5:
						echo 'Unsupported target file format!';
						break;
					case 6:
						echo 'GD library version does not support target file format!';
						break;
					case 7:
						echo 'GD library is not installed!';
						break;
				}
			}
		# if no errors
		} else {
			echo '';
		}
	}

	/**
	 * Determine the GD version
	 * @author: Code from php.net
	 */
	static function gdVersion($user_ver = 0) {
		if (! extension_loaded('gd')) {
			return;
		}
		static $gd_ver = 0;

		# Just accept the specified setting if it's 1.
		if ($user_ver == 1) {
			$gd_ver = 1;
			return 1;
		}
		# Use the static variable if function was called previously.
		if ($user_ver != 2 && $gd_ver > 0) {
			return $gd_ver;
		}
		# Use the gd_info() function if possible.
		if (function_exists('gd_info')) {
			$ver_info = gd_info();
			preg_match('/\d/', $ver_info['GD Version'], $match);
			$gd_ver = $match[0];
			return $match[0];
		}
		# If phpinfo() is disabled use a specified / fail-safe choice...
		if (preg_match('/phpinfo/', ini_get('disable_functions'))) {
			if ($user_ver == 2) {
				$gd_ver = 2;
				return 2;
			} else {
				$gd_ver = 1;
				return 1;
			}
		}
		# ...otherwise use phpinfo().
		ob_start();
		phpinfo(8);
		$info = ob_get_contents();
		ob_end_clean();
		$info = stristr($info, 'gd version');
		preg_match('/\d/', $info, $match);
		$gd_ver = $match[0];

		return $match[0];
	}

	/**
	 * Creates image information of an image
	 *
	 * @param string $image The image name
	 * @param array $settings
	 * @param string $type event or venue
	 *
	 * @return imagedata if available
	 */
	static function flyercreator($image, $type) {

		# load settings
		$settings = JemHelper::config();

		# import filesystem
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');


		switch($type) {
			case 'event':
				$folderx = 'events';
				break;

			case 'category':
				$folderx = 'categories';
				break;

			case 'venue':
				$folderx = 'venues';
				break;
		}


		if ($image) {

			# are we dealing with an image of previous JEM/EL versions
			if (strpos($image,'images/') !== false) {

			} else {
				$image = 'images/jem/'.$folderx.'/'.$image;
			}

			$filename = basename($image);
			$dirname = dirname($image);


			# skip thumb generating if it's a file within a thumb directory
			if (strpos($dirname,'small/') !== false) {
			 	$thumb = false;
			} else {
				$thumb = true;
			}

			if ($settings->gddisabled == 1 && !file_exists(JPATH_SITE.'/'.$dirname.'/small/'.$filename) && $thumb == true) {
				# Create thumbnail if enabled and it does not exist already

				$filepath 	= JPATH_SITE.'/'.$dirname.'/'.$filename;
				$save 		= JPATH_SITE.'/'.$dirname.'/small/'.$filename;

				$savefolder = JPATH_SITE.'/'.$dirname.'/small/';
				if (!JFolder::exists($savefolder)) {
					JFolder::create($savefolder);
					$file_content = '<!DOCTYPE html><title></title>';
					JFile::write($savefolder.'/index.html', $file_content);
				}

				JemImage::thumb($filepath, $save, $settings->imagewidth, $settings->imagehight);
			}

			# we did create a thumb
			# @todo: checkout what happens when the above is not triggered

			# set paths
			$oimage['original'] = $image;
			$oimage['thumb'] 	= $dirname.'/small/'.$filename;

			# get imagesize of the original
			$iminfo = @getimagesize($image);

			# if the width or height is too large this formula will resize them accordingly
			if (($iminfo[0] > $settings->imagewidth) || ($iminfo[1] > $settings->imagehight)) {

				$iRatioW = $settings->imagewidth / $iminfo[0];
				$iRatioH = $settings->imagehight / $iminfo[1];

				if ($iRatioW < $iRatioH) {
					$oimage['width'] 	= round($iminfo[0] * $iRatioW);
					$oimage['height'] 	= round($iminfo[1] * $iRatioW);
				} else {
					$oimage['width'] 	= round($iminfo[0] * $iRatioH);
					$oimage['height'] 	= round($iminfo[1] * $iRatioH);
				}
			} else {
				$oimage['width'] 	= $iminfo[0];
				$oimage['height'] 	= $iminfo[1];
			}

			if (JFile::exists(JPATH_SITE.'/'.$dirname.'/small/'.$filename)) {
				#get imagesize of the thumbnail
				$thumbiminfo = @getimagesize(JPATH_SITE.'/'.$dirname.'/small/'.$filename);
				$oimage['thumbwidth'] 	= $thumbiminfo[0];
				$oimage['thumbheight'] 	= $thumbiminfo[1];
			}
			return $oimage;
		}
		return false;
	}

	static function check($file, $jemsettings) {
		jimport('joomla.filesystem.file');

		$sizelimit = $jemsettings->sizelimit*1024; //size limit in kb
		$imagesize = $file['size'];

		# check if the upload is an image...getimagesize will return false if not
		if (!getimagesize($file['tmp_name'])) {
			JError::raiseWarning(100, JText::_('COM_JEM_UPLOAD_FAILED_NOT_AN_IMAGE').': '.htmlspecialchars($file['name'], ENT_COMPAT, 'UTF-8'));
			return false;
		}

		# check if the imagefiletype is valid
		$fileext = strtolower(JFile::getExt($file['name']));

		$allowable = array ('gif', 'jpg', 'png');
		if (!in_array($fileext, $allowable)) {
			JError::raiseWarning(100, JText::_('COM_JEM_WRONG_IMAGE_FILE_TYPE').': '.htmlspecialchars($file['name'], ENT_COMPAT, 'UTF-8'));
			return false;
		}

		# Check filesize
		if ($imagesize > $sizelimit) {
			JError::raiseWarning(100, JText::_('COM_JEM_IMAGE_FILE_SIZE').': '.htmlspecialchars($file['name'], ENT_COMPAT, 'UTF-8'));
			return false;
		}

		# XSS check
		$xss_check = file_get_contents($file['tmp_name'], false, 256);
		$html_tags = array('abbr','acronym','address','applet','area','audioscope','base','basefont','bdo','bgsound','big','blackface','blink','blockquote','body','bq','br','button','caption','center','cite','code','col','colgroup','comment','custom','dd','del','dfn','dir','div','dl','dt','em','embed','fieldset','fn','font','form','frame','frameset','h1','h2','h3','h4','h5','h6','head','hr','html','iframe','ilayer','img','input','ins','isindex','keygen','kbd','label','layer','legend','li','limittext','link','listing','map','marquee','menu','meta','multicol','nobr','noembed','noframes','noscript','nosmartquotes','object','ol','optgroup','option','param','plaintext','pre','rt','ruby','s','samp','script','select','server','shadow','sidebar','small','spacer','span','strike','strong','style','sub','sup','table','tbody','td','textarea','tfoot','th','thead','title','tr','tt','ul','var','wbr','xml','xmp','!DOCTYPE', '!--');
		foreach($html_tags as $tag) {
			# A tag is '<tagname ', so we need to add < and a space or '<tagname>'
			if(stristr($xss_check, '<'.$tag.' ') || stristr($xss_check, '<'.$tag.'>')) {
				JError::raiseWarning(100, JText::_('COM_JEM_WARN_IE_XSS'));
				return false;
			}
		}

		return true;
	}
}
?>