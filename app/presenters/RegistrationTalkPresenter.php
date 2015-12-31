<?php
/**
 * Created by PhpStorm.
 * User: norik
 * Date: 27.12.15
 * Time: 1:59
 */


namespace App\Presenters;

use Nette,
	Nette\Application\UI\Form,
	Nette\Utils\DateTime,
	Nette\Mail\Message,
	Nette\Mail\IMailer,
	Helpers;



class RegistrationTalkPresenter extends Nette\Application\UI\Presenter
{

	/** @var  @var Nette\Databe\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
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
		$users = $this->database->table('user');
		$temp = $users->select('id')->where('email LIKE ?', $values->email)->limit(1)->fetch();

		if ($temp) {  //nepomohlo sem dat ani $temp

			$this->database->table('talk')->insert([
				'name' => $values->name,
				'subject' => $values->subject,
				'decription' => $values->decription,
				'lenght' => $values->lenght,
				'user_id' => $temp,
			]);

			$mail = new Message;
			$mail->setFrom('BrNOC bot <bot@brnoc.cz>')
				->addTo($values->email)
				->setSubject('Potvrzení příhlášení')
				->setBody("Byl jsi přihlášen jako účastník BrNOCi 2015. \n \nBrNOC tým");


			//$this->mailer->send($mail);
			//not done

			$this->flashMessage('Registrace proběhla úspěšně', 'success');
		} else {
			$this->flashMessage('ÚČASTNÍK s tímto emailem neexistuje', 'danger');
		}
		$this->redirect('this');

	}

}