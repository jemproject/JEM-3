<?php
/**
 * JEM Package
 * @package JEM.Package
 *
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ('_JEXEC') or die;

/**
 * JEM package installer script.
 */
class Pkg_JemInstallerScript {

	private $oldRelease = "";
	private $newRelease = "";
	
	/**
	 * List of supported versions. Newest version first!
	 * @var array
	 */
	protected $versions = array(
		'PHP' => array (
			'5.3' => '5.3.10',
			'0' => '5.4.14' // Preferred version
			),
		'MySQL' => array (
			'5.1' => '5.1',
			'0' => '5.5' // Preferred version
			),
		'Joomla!' => array (
			'3.0' => '3.3.0', 
			'0' => '3.3.0' // Preferred version
			)
		);

	/**
	 * List of required PHP extensions.
	 * @var array
	 */
	protected $extensions = array ('gd', 'json', 'pcre'
			, 'iconv' /* import */
			, 'ctype', 'SimpleXML' /* iCalCreator */
		);

	public function install($parent) {
			//$this->getHeader();
		return true;
	}

	public function discover_install($parent) {
		return self::install($parent);
	}

	public function update($parent) {
			//$this->getHeader();
		return self::install($parent);
	}

	public function uninstall($parent) {
		return true;
	}

	public function preflight($type, $parent) {
		/** @var JInstallerComponent $parent */
		$manifest = $parent->getParent()->getManifest();

		// Prevent installation if requirements are not met.
		if (!$this->checkRequirements($manifest->version)) return false;

		// abort if the release being installed is not newer than the currently installed version
		if ($type == 'update') {
			// Installed component version
			$this->oldRelease = $this->getParam('version');

			// Installing component version as per Manifest file
			$this->newRelease = $parent->get('manifest')->version;

			$this->setUpdateServer();
			
			/*
			if ($this->oldRelease < 3) {
				Jerror::raiseNotice(100,JText::sprintf('PKG_JEM_INSTALLATION_PREVENTINSTALL',$this->oldRelease));	
				return false;	
			}
			*/
		}
		return true;
	}

	public function makeRoute($uri) {
		return JRoute::_($uri, false);
	}

	public function postflight($type, $parent) {
		// Clear Joomla system cache.
		/** @var JCache|JCacheController $cache */
		$cache = JFactory::getCache();
		$cache->clean('_system');

		// Remove all compiled files from APC cache.
		if (function_exists('apc_clear_cache')) {
			@apc_clear_cache();
		}

		if ($type == 'uninstall') return true;
		
		if ($type == 'install' || $type == 'update') {
			/* $parent->getParent()->setRedirectURL(JRoute::_('index.php?option=com_jem&view=main', false)); */
		}
		
		$this->enablePlugin('content', 'jem');
	//	$this->enablePlugin('search', 'jem');
	//	$this->enablePlugin('jem', 'mailer');
	
		return true;
	}

	function enablePlugin($group, $element) {
		$plugin = JTable::getInstance('extension');
		if (!$plugin->load(array('type'=>'plugin', 'folder'=>$group, 'element'=>$element))) {
			return false;
		}
		$plugin->enabled = 1;
		return $plugin->store();
	}

	public function checkRequirements($version) {
		$db = JFactory::getDbo();
		$pass  = $this->checkVersion('PHP', phpversion());
		$pass &= $this->checkVersion('Joomla!', JVERSION);
		$pass &= $this->checkVersion('MySQL', $db->getVersion ());
		$pass &= $this->checkDbo($db->name, array('mysql', 'mysqli'));
		$pass &= $this->checkExtensions($this->extensions);
		$pass &= $this->checkMagicQuotes();
		return $pass;
	}

	// Internal functions

	protected function checkVersion($name, $version) {
		$app = JFactory::getApplication();

		$major = $minor = 0;
		foreach ($this->versions[$name] as $major=>$minor) {
			if (!$major || version_compare($version, $major, '<')) continue;
			if ($minor && version_compare($version, $minor, '>=')) return true;
			break;
		}
		if (!$major) $minor = reset($this->versions[$name]);
		$recommended = end($this->versions[$name]);
		if ($minor) {
			$app->enqueueMessage(sprintf("%s %s is not supported. Minimum required version is %s %s, but it is highly recommended to use %s %s or later.", $name, $version, $name, $minor, $name, $recommended), 'notice');
		} else {
			$app->enqueueMessage(sprintf("%s %s is not supported. It is highly recommended to use %s %s or later.", $name, $version, $name, $recommended), 'notice');
		}
		return false;
	}

	protected function checkDbo($name, $types) {
		$app = JFactory::getApplication();

		if (in_array($name, $types)) {
			return true;
		}
		$app->enqueueMessage(sprintf("Database driver '%s' is not supported. Please use MySQL instead.", $name), 'notice');
		return false;
	}

	protected function checkExtensions($extensions) {
		$app = JFactory::getApplication();

		$pass = 1;
		foreach ($extensions as $name) {
			if (!extension_loaded($name)) {
				$pass = 0;
				$app->enqueueMessage(sprintf("Required PHP extension '%s' is missing. Please install it into your system.", $name), 'notice');
			}
		}
		return $pass;
	}

	protected function checkMagicQuotes() {
		$app = JFactory::getApplication();

		// Abort if Magic Quotes are enabled, it was removed from phpversion 5.4
		if (version_compare(phpversion(), '5.4', '<')) {
			if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
				$app->enqueueMessage("Magic Quotes are enabled. JEM requires Magic Quotes to be disabled.", 'notice');
				return false;
			}
		}
		return true;
	}
	
	protected function setUpdateServer() {
		$app = JFactory::getApplication();
		
		$version = array('3.0.1','3.0.2','3.0.3','3.0.4','3.0.5','3.0.6');
		
		if (in_array($this->oldRelease,$version)) {
			// Remove entry in table update_sites
			$db = JFactory::getDbo();
			$query	= $db->getQuery(true);
			$query->delete('#__update_sites');
			$query->where('name = '.$db->q('JEM Update Site'));
			$db->setQuery($query);
			$db->execute(); 
		}
		
		return true;
		
	}
	
	
	/**
	 * Helper method that outputs a short JEM header with logo and text
	 */
	private function getHeader() {
		?>
		<img src="../media/com_jem/images/jemlogo.png" alt="" style="float:left; padding-right:20px;" />
		<h1><?php echo JText::_('PKG_JEM'); ?></h1>
		<p class="small"><?php echo JText::_('PKG_JEM_INSTALLATION_HEADER'); ?></p>
		<?php
	}
	
	/**
	 * Get a parameter from the manifest file (actually, from the manifest cache).
	 *
	 * @param $name  The name of the parameter
	 *
	 * @return The parameter
	 */
	private function getParam($name) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('manifest_cache')->from('#__extensions')->where(array("type = 'package'", "element = 'pkg_jem'"));
		$db->setQuery($query);
		$manifest = json_decode($db->loadResult(), true);
		return $manifest[$name];
	}
}
