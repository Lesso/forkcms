<?php

/**
 * FrontendSnippetsModel
 *
 * In this file we store all generic functions that we will be using in the blog module
 *
 *
 * @package		frontend
 * @subpackage	snippets
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class FrontendSnippetsModel
{
	/**
	 * Get a snippet
	 *
	 * @return	array
	 * @param	string $id
	 */
	public static function get($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = FrontendModel::getDB();

		// get data
		return (array) $db->getRecord('SELECT s.id, s.title, s.content
										FROM snippets AS s
										WHERE s.id = ? AND s.status = ? AND s.hidden = ? AND s.language = ?;',
										array($id, 'active', 'N', FRONTEND_LANGUAGE));
	}
}

?>