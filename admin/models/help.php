<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Model: Help
 */
class JEMModelHelp extends JModelLegacy
{
	protected $help_search = null;
	protected $page = null;
	protected $lang_tag = null;
	protected $toc = null;

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Method to get the help search string
	 */
	public function &getHelpSearch()
	{
		if (is_null($this->help_search)) {
			$this->help_search = JFactory::getApplication()->input->getString('helpsearch');
		}

		return $this->help_search;
	}

	/**
	 * Method to get the page
	 */
	public function &getPage()
	{
		if (is_null($this->page))
		{
			$page = JFactory::getApplication()->input->get('page', 'JHELP_START_HERE');
			$this->page = JHelp::createUrl($page);
		}

		return $this->page;
	}

	/**
	 * Method to get the lang tag
	 */
	public function getLangTag()
	{
		if (is_null($this->lang_tag))
		{
			$lang = JFactory::getLanguage();
			$this->lang_tag = $lang->getTag();

			if (!is_dir(JPATH_SITE .'/administrator/components/com_jem/help/' . $this->lang_tag))
			{
				// Use english as fallback
				$this->lang_tag = 'en-GB';
			}
		}

		return $this->lang_tag;
	}

	/**
	 * Method to get the toc
	 *
	 * @return  array  Table of contents
	 */
	public function &getToc()
	{
		if (is_null($this->toc))
		{
			// Get vars
			$lang_tag = $this->getLangTag();
			$help_search = $this->getHelpSearch();

			// New style - Check for a TOC JSON file
			if (file_exists(JPATH_SITE .'/administrator/components/com_jem/help/' . $lang_tag . '/toc.json'))
			{
				$data = json_decode(file_get_contents(JPATH_SITE .'/administrator/components/com_jem/help/' . $lang_tag . '/toc.json'));

				// Loop through the data array
				foreach ($data as $key => $value)
				{
					$this->toc[$key] = JText::_('COM_JEM_HELP_' . $value);
				}
			}
			else
			{
				// Get Help files
				jimport('joomla.filesystem.folder');
				$files = JFolder::files(JPATH_SITE .'/administrator/components/com_jem/help/' . $lang_tag, '\.xml$|\.html$');
				$this->toc = array();

				foreach ($files as $file)
				{
					$buffer = file_get_contents(JPATH_SITE .'/administrator/components/com_jem/help/' . $lang_tag . '/' . $file);

					if (preg_match('#<title>(.*?)</title>#', $buffer, $m))
					{
						$title = trim($m[1]);

						if ($title)
						{
							// Translate the page title
							$title = JText::_($title);

							// Strip the extension
							/*$file = preg_replace('#\.xml$|\.html$#', '', $file);*/

							if ($help_search)
							{
								if (JString::strpos(JString::strtolower(strip_tags($buffer)), JString::strtolower($help_search)) !== false)
								{
									// Add an item in the Table of Contents
									$this->toc[$file] = $title;
								}
							}
							else
							{
								// Add an item in the Table of Contents
								$this->toc[$file] = $title;
							}
						}
					}
				}
			}

			// Sort the Table of Contents
			asort($this->toc);
		}

		return $this->toc;
	}
}
