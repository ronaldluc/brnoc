<?php
/**
 * @author Ronald Luc
 */

namespace App\Model;

use Nette,
	Nette\Application\UI\Form,
	Nette\Utils\DateTime,
	Nette\Mail\Message,
	Nette\Mail\IMailer,
	Helpers;

class RegistrationTalkModel
{
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	public function createTalk($values)
	{
		$users = $this->database->table('user');
		$temp = $users->select('id')->where('email = ?', $values->email)->limit(1)->fetch();

		if ($temp) {  //nepomohlo sem dat ani $temp

			$this->database->table('talk')->insert([
				'name' => $values->name,
				'subject' => $values->subject,
				'decription' => $values->decription,
				'lenght' => $values->lenght,
				'user_id' => $temp,
				'date' => new DateTime(),
			]);

			$mail = new Message;
			$mail->setFrom('BrNOC bot <bot@brnoc.cz>')
				->addTo($values->email)
				->setSubject('Potvrzení příhlášení')
				->setBody("Byl jsi přihlášen jako účastník BrNOCi 2015. \n \nBrNOC tým");


			//$this->mailer->send($mail);
			//not done
		}

		return $temp;
	}
}