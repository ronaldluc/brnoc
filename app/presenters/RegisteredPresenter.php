<?php
/**
 * Created by PhpStorm.
 * User: norik
 * Date: 25.12.15
 * Time: 2:22
 */

namespace App\Presenters;

use Nette,
	App\Model;



class RegisteredPresenter extends Nette\Application\UI\Presenter
{
	/** @var  @var Nette\Databe\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	public function renderDefault()
	{
		$this->template->users = $this->database->table('user')
			->order('created DESC');
	}

	public function renderAdmin()
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:admin');
		}

		$this->template->users = $this->database->table('user')
			->order('created DESC');
	}

	public function handleDelete($id)
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:admin');
		}

		$this->database->table('user')->where('id = ?', $id)->delete();

		//$this->redirect();
	}
}