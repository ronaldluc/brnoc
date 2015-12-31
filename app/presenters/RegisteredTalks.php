<?php
/**
 * Created by PhpStorm.
 * User: norik
 * Date: 27.12.15
 * Time: 15:29
 */

namespace App\Presenters;

use Nette,
	App\Model;



class RegisteredTalksPresenter extends Nette\Application\UI\Presenter
{
	/** @var  @var Nette\Databe\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	public function renderDefault()
	{
		$this->template->talks = $this->database->table('talk')->where('hidden = ?', 0)
			->order('name DESC');
	}

	public function renderAdminHidden()
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:admin');
		}

		$this->template->talksHidden = $this->database->table('talk')
			->where('hidden = ?', 1)
			->order('name DESC');
	}

	public function renderAdminShown()
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:admin');
		}

		$this->template->talksShown = $this->database->table('talk')
			->where('hidden = ?', 0)
			->order('name DESC');
	}


	public function handleDelete($id)
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:admin');
		}

		$this->database->table('talk')->where('id = ?', $id)->delete();

		//$this->redirect();
	}

	public function handleApprove($id)
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:admin');
		}

		$this->database->table('talk')->where('id = ?', $id)->update(Array('hidden' => 0));

		//$this->redirect();
	}
}