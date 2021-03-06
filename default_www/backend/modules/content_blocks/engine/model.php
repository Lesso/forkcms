<?php

/**
 * In this file we store all generic functions that we will be using in the content_blocks module
 *
 * @package		backend
 * @subpackage	content_blocks
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class BackendContentBlocksModel
{
	/**
	 * Overview of the items
	 *
	 * @var	string
	 */
	const QRY_BROWSE = 'SELECT i.id, i.title
						FROM content_blocks AS i
						WHERE i.status = ? AND i.language = ?';


	/**
	 * Overview of the revisions for an item
	 *
	 * @var	string
	 */
	const QRY_BROWSE_REVISIONS = 'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
									FROM content_blocks AS i
									WHERE i.status = ? AND i.id = ? AND i.language = ?
									ORDER BY i.edited_on DESC';


	/**
	 * Delete an item.
	 *
	 * @return	void
	 * @param	int $id		The id of the record to delete.
	 */
	public static function delete($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB(true);

		// get item
		$item = self::get($id);

		// build extra
		$extra = array('id' => $item['extra_id'],
						'module' => 'content_blocks',
						'type' => 'widget',
						'action' => 'detail');

		// delete extra
		$db->delete('pages_extras', 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));

		// update blocks with this item linked
		$db->update('pages_blocks', array('extra_id' => null, 'html' => ''), 'extra_id = ?', array($item['extra_id']));

		// delete all records
		$db->delete('content_blocks', 'id = ?', $id);
	}


	/**
	 * Does the item exist.
	 *
	 * @return	bool
	 * @param	int $id							The id of the record to check for existence.
	 * @param	bool[optional] $activeOnly		Only check in active items?
	 */
	public static function exists($id, $activeOnly = true)
	{
		// get db
		$db = BackendModel::getDB();

		// if the item should also be active, there should be at least one row to return true
		if((bool) $activeOnly) return (bool) $db->getVar('SELECT COUNT(i.id)
															FROM content_blocks AS i
															WHERE i.id = ? AND i.status = ?',
															array((int) $id, 'active'));

		// fallback, this doesn't take the active status in account
		return (bool) $db->getVar('SELECT COUNT(i.id)
									FROM content_blocks AS i
									WHERE i.revision_id = ?',
									array((int) $id));
	}


	/**
	 * Get all data for a given id.
	 *
	 * @return	array
	 * @param	int $id		The id for the record to get.
	 */
	public static function get($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on
															FROM content_blocks AS i
															WHERE i.id = ? AND i.status = ?
															LIMIT 1',
															array((int) $id, 'active'));
	}


	/**
	 * Get the maximum id.
	 *
	 * @return	int
	 */
	public static function getMaximumId()
	{
		return (int) BackendModel::getDB()->getVar('SELECT MAX(i.id) FROM content_blocks AS i LIMIT 1');
	}


	/**
	 * Get all data for a given revision.
	 *
	 * @return	array
	 * @param	int $id				The Id for the item wherefor you want a revision.
	 * @param	int $revisionId		The Id of the revision.
	 */
	public static function getRevision($id, $revisionId)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on
															FROM content_blocks AS i
															WHERE i.id = ? AND i.revision_id = ?
															LIMIT 1',
															array((int) $id, (int) $revisionId));
	}


	/**
	 * Add a new item.
	 *
	 * @return	int
	 * @param	array $item			The data to insert.
	 */
	public static function insert(array $item)
	{
		// get db
		$db = BackendModel::getDB(true);

		// build extra
		$extra = array('module' => 'content_blocks',
						'type' => 'widget',
						'label' => 'ContentBlocks',
						'action' => 'detail',
						'data' => null,
						'hidden' => 'N',
						'sequence' => $db->getVar('SELECT MAX(i.sequence) + 1
													FROM pages_extras AS i
													WHERE i.module = ?', array('content_blocks')));
		if(is_null($extra['sequence'])) $extra['sequence'] = $db->getVar('SELECT CEILING(MAX(i.sequence) / 1000) * 1000
																			FROM pages_extras AS i');

		// insert extra
		$item['extra_id'] = $db->insert('pages_extras', $extra);
		$extra['id'] = $item['extra_id'];

		// insert and return the new revision id
		$item['revision_id'] = $db->insert('content_blocks', $item);

		// update extra (item id is now known)
		$extra['data'] = serialize(array('id' => $item['id'],
											'extra_label' => $item['title'],
											'language' => $item['language'],
											'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $item['id']));
		$db->update('pages_extras', $extra, 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));

		// return the new revision_id
		return $item['revision_id'];
	}


	/**
	 * Update an existing item.
	 *
	 * @return	int
	 * @param	array $item			The new data.
	 */
	public static function update(array $item)
	{
		// get db
		$db = BackendModel::getDB(true);

		// build extra
		$extra = array('id' => $item['extra_id'],
						'module' => 'content_blocks',
						'type' => 'widget',
						'label' => 'ContentBlocks',
						'action' => 'detail',
						'data' => serialize(array('id' => $item['id'],
													'extra_label' => $item['title'],
													'language' => $item['language'],
													'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $item['id'])),
						'hidden' => 'N');

		// update extra
		$db->update('pages_extras', $extra, 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));

		// archive all older versions
		$db->update('content_blocks', array('status' => 'archived'), 'id = ? AND language = ?', array($item['id'], BL::getWorkingLanguage()));

		// insert new version
		$item['revision_id'] = $db->insert('content_blocks', $item);

		// how many revisions should we keep
		$rowsToKeep = (int) BackendModel::getModuleSetting('content_blocks', 'max_num_revisions', 20);

		// get revision-ids for items to keep
		$revisionIdsToKeep = (array) $db->getColumn('SELECT i.revision_id
														FROM content_blocks AS i
														WHERE i.id = ? AND i.language = ? AND i.status = ?
														ORDER BY i.edited_on DESC
														LIMIT ?',
														array($item['id'], BL::getWorkingLanguage(), 'archived', $rowsToKeep));

		// delete other revisions
		if(!empty($revisionIdsToKeep)) $db->delete('content_blocks', 'id = ? AND language = ? AND status = ? AND revision_id NOT IN (' . implode(', ', $revisionIdsToKeep) . ')', array($item['id'], BL::getWorkingLanguage(), 'archived'));

		// return the new revision_id
		return $item['revision_id'];
	}
}

?>