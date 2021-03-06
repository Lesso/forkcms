<?php

/**
 * BackendMailmotorAddCampaign
 * This is the add-action, it will display a form to create a new campaign
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorAddCampaign extends BackendBaseActionAdd
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load the form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('add');

		// create elements
		$this->frm->addText('name');
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// shorten fields
			$txtName = $this->frm->getField('name');

			// validate fields
			$txtName->isFilled(BL::err('NameIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['name'] = $txtName->getValue();
				$item['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');

				// insert the item
				$id = BackendMailmotorModel::insertCampaign($item);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('campaigns') . '&report=added&var=' . urlencode($item['name']) . '&highlight=id-' . $id);
			}
		}
	}
}

?>