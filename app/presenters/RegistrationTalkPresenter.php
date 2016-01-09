<?php
/**
 * Created by PhpStorm.
 * User: norik
 * Date: 27.12.15
 * Time: 1:59
 */


namespace App\Presenters;

use App\Model\RegistrationTalkModel;

use Nette,
	Nette\Application\UI\Form,
	Nette\Utils\DateTime,
	Nette\Mail\Message,
	Nette\Mail\IMailer,
	Helpers;



class RegistrationTalkPresenter extends Nette\Application\UI\Presenter
{

	/** @var RegistrationTalkModel */
	private $registrationTalkModel;


	public function __construct(RegistrationTalkModel $registrationTalkModel)
	{
		$this->registrationTalkModel = $registrationTalkModel;
	}

	protected function createComponentRegisterTalkForm()
	{
		$form = new Form;

		$form->addText('name', 'Název')
			->setRequired();

		$form->addText('subject', 'Obor')
			->setRequired();

		$form->addTextArea('decription', 'Popis')
			->setRequired()->addRule(Form::MAX_LENGTH, 'Poznámka je příliš dlouhá', 1000);

		//TODO replace for any lenght
		$form->addRadioList('lenght', 'Délka', array(10 => '15 minut', 25 => '25 minut'))
			->setRequired();

		$form->addText('email', 'Email')
			->setRequired()->addRule($form::EMAIL);

		$form->addSubmit('send', 'Registrovat přednášku');

		$form->onSuccess[] = array($this, 'registerTalkFormSucceeded');

		Helpers::bootstrapForm($form);

		return $form;
	}


	public function registerTalkFormSucceeded($form, $values)
	{
		$temp = $this->registrationTalkModel->createTalk($values);

		if ($temp)
		{
			$this->flashMessage('Registrace proběhla úspěšně', 'success');
		} else {
			$this->flashMessage('ÚČASTNÍK s tímto emailem neexistuje', 'danger');
		}
		$this->redirect('this');

	}

}