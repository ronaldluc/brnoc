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
	Nette\Mail\SendmailMailer,
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
		$selection = $this->database->table('user');
		$temp = $selection->where('email LIKE ?', $values->email)->fetch();
		if (!$temp) {
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
			$mail->setFrom('BrNOCbot <bot@brnoc.cz>')
				->addTo($values->email, $values->name.' '.$values->last_name)
				->addTo('ron.norik@gmail.com')
				->setSubject('Potvrzení registrace | BrNOC 2016')
//				->setBody("Ahoj, \nbyl jsi přihlášen jako účastník BrNOCi 2016. \n
//					Důležité informace: \n
//					Akce se koná od 16:00 15. 4. do 10:00 16. 4.
//					S sebou něco na spaní (třeba spacák a karimatku), pokud jsi velký hladovec, vem si další jídlo,
//					ale se základním pokrytím hladu můžeš počítat (#bagety).\n
//					\nBrNOC tým");
				->setHTMLBody("Ahoj, byl jsi přihlášen jako účastník BrNOCi 2016.<br><br>
					<p>Důležité informace: <br>
					Akce se koná od 16:00 15. 4. do 10:00 16. 4. <br>
					S sebou něco na spaní (třeba spacák a karimatku), pokud jsi velký hladovec, vem si další jídlo,
					ale se základním pokrytím hladu můžeš počítat (#bagety).</p><br>
					V případě jakýchkoliv otázek <a href=\"mailto:ron.norik@gmail.com\">nás kontaktuj</a>.
					<br><br><a href=\"http://www.brnoc.cz/\">BrNOC tým</a>");



			$mailer = new SendmailMailer;

			$mailer->send($mail);


			$this->flashMessage('Registrace proběhla úspěšně. Zaslali jsme ti email s dalšími informacemi.', 'success');
		} else {
			$this->flashMessage('Tento email už byl použit.', 'danger');
		}
		$this->redirect('this');
	}

	//private function

}