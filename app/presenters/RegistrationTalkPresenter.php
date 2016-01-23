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

			$mail = new Message;
			$mail->setFrom('<pus@brnoc.cz>', 'Pravoúhelný sněm')
				->addTo($values->email, 'Přednášející')
				->addTo('pus@brnoc.cz')
				->setSubject('Potvrzení registrace přednášky | BrNOC 2016')
//				->setBody("Ahoj, \nbyl jsi přihlášen jako účastník BrNOCi 2016. \n
//					Důležité informace: \n
//					Akce se koná od 16:00 15. 4. do 10:00 16. 4.
//					S sebou něco na spaní (třeba spacák a karimatku), pokud jsi velký hladovec, vem si další jídlo,
//					ale se základním pokrytím hladu můžeš počítat (#bagety).\n
//					\nBrNOC tým");
				->setHTMLBody("Ahoj, tvoje přednáška ".$values->name." byla přihlášena do systému.<br><br>
					<p>Důležité informace: <br>
					Akce se koná od 16:00 15. 4. do 10:00 16. 4. <br>
					S sebou něco na spaní (třeba spacák a karimatku), pokud jsi velký hladovec, vem si další jídlo,
					ale se základním pokrytím hladu můžeš počítat (#bagety).</p><br>
					V případě jakýchkoliv otázek <a href=\"mailto:pus@brnoc.cz\">nás kontaktuj</a>.
					<br><br><a href=\"http://www.brnoc.cz/\">BrNOC tým</a>");



			$mailer = new SendmailMailer;

			$mailer->send($mail);
		} else {
			$this->flashMessage('Účastník s tímto emailem neexistuje', 'danger');
		}
		$this->redirect('this');

	}

}