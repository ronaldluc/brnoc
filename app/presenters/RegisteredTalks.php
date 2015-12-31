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
		$this->template->talks = $this->database->table('talk')
			->order('name DESC');
	}

	public function renderAdmin()
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:admin');
		}

		$this->template->talks = $this->database->table('talk')
			->order('name DESC');
	}


}