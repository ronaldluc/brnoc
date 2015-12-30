<?php
/**
 * Created by PhpStorm.
 * User: norik
 * Date: 29.12.15
 * Time: 23:27
 */

namespace App\Presenters;

use Nette,
	Nette\Application\UI,
	Helpers;

class SignPresenter extends Nette\Application\UI\Presenter
{
	protected function createComponentSignAdminForm()
	{
		$form = new Nette\Application\UI\Form;

		$form->addText('username', 'Uživatelské jméno:')
			->setRequired();

		$form->addPassword('password', 'Heslo:')
			->setRequired();

		$form->addSubmit('send', 'Přihlásit');

		$form->onSuccess[] = array($this, 'signAdminFormSucceeded');

		Helpers::bootstrapForm($form);

		return $form;
	}

	public function signAdminFormSucceeded($form)
	{
		$values = $form->values;

		try {
			$this->getUser()->login($values->username, $values->password);
			$this->flashMessage('Jsi přihlášen.', 'success');
			$this->redirect('Homepage:');
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError('Nesprávné heslo. V případě potíží kontaktuj Rona');
			$this->flashMessage('Nesprávné heslo. V případě potíží kontaktuj Rona', 'danger');
		}
	}
}