<?php
/**
 * Created by PhpStorm.
 * User: norik
 * Date: 25.12.15
 * Time: 19:05
 */

namespace App\Presenters;

use Nette,
	Nette\Application\UI\Form,
	Nette\Utils\DateTime,
	Nette\Mail\Message,
	Nette\Mail\IMailer,
	Helpers;



class RegistrationUserPresenter extends Nette\Application\UI\Presenter
{
	/** @var  @var Nette\Databe\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	protected function createComponentRegisterUserForm()
	{
		$form = new Form;

		$form->addText('name', 'Jméno')
			->setRequired();

		$form->addText('last_name', 'Příjmení')
			->setRequired();

		$form->addText('nick', 'Přezdívka');

		$form->addText('school', 'Škola')
			->setRequired();

		$form->addText('email', 'Email')
			->setRequired()->addRule($form::EMAIL);

		$form->addText('mystery_topic', 'Zájem o přednášku na téma')
			->setRequired();

		$form->addCheckbox('mystery_volunteer', 'Zájem o přednášení v mystery room');

		$form->addText('favorite_math', 'Oblíbená matematická věta');

		$form->addText('favorite_physics', 'Oblíbený fyzikální princip');

		$form->addText('favorite_chemistry', 'Oblíbená organická sloučenina');

		$form->addSubmit('send', 'Registrovat se');

		$form->onSuccess[] = array($this, 'registerUserFormSucceeded');

		Helpers::bootstrapForm($form);

		return $form;
	}

	public function registerUserFormSucceeded($form, $values)
	{
		$this->database->table('user')->insert([
			'name' => $values->name,
			'last_name' => $values->last_name,
			'nick' => $values->nick,
			'school' => $values->school,
			'email' => $values->email,
			'mystery_topic' => $values->mystery_topic,
			'mystery_volunteer' => $values->mystery_volunteer,
			'favorite_math' => $values->favorite_math,
			'favorite_physics' => $values->favorite_physics,
			'favorite_chemistry' => $values->favorite_chemistry,
			'created' => new DateTime(),
		]);

		$mail = new Message;
		$mail->setFrom('BrNOC bot <bot@brnoc.cz>')
			->addTo($values->email)
			->setSubject('Potvrzení příhlášení')
			->setBody("Byl jsi přihlášen jako účastník BrNOCi 2015. \n \nBrNOC tým");


		//$this->mailer->send($mail);
		//not done

		$this->flashMessage('Registrace proběhla úspěšně', 'success');
		$this->redirect('this');
	}

	//private function

}