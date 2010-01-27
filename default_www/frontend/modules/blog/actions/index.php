<?php

/**
 * Sitemap
 *
 * This is the overview-action
 *
 * @package		frontend
 * @subpackage	sitemap
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBlogIndex extends FrontendBaseBlock
{
	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call the parent, will add js
		parent::execute();

		// load template
		$this->loadTemplate();
	}
}
?>