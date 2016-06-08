<?php
namespace ImLike\Forms;

use ImLike\Localization;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\Regex as RegexValidator;

class ForgotPasswordForm extends Form{

	private $language = "en";

	public function initialize(){

		$password = new Password('forgot-password', array(
			'placeholder' => Localization::getText($this->language, __METHOD__ . "-passwordPlaceholder"),
			'class' => 'form-control',
			'id' => 'forgot-password',
			'maxlength' => '128'
		));

		$password->addValidators(array(
			new StringLength(array(
				'max' => 128,
				'min' => 6,
				'messageMaximum' => Localization::getText($this->language, __METHOD__ . "-passwordMessageMax"),
				'messageMinimum' => Localization::getText($this->language, __METHOD__ . "-passwordMessageMin"),
			))
		));

		$password->setLabel(Localization::getText($this->language, __METHOD__ . "-passwordLabel"));

		$this->add($password);

		$passwordRetyped = new Password('forgot-password-retyped', array(
			'placeholder' => Localization::getText($this->language, __METHOD__ . "-passwordRetypedPlaceholder"),
			'class' => 'form-control',
			'id' => 'forgot-password-retyped',
			'maxlength' => '128'
		));

		$passwordRetyped->addValidators(array(
			new Confirmation(array(
				'with'   => 'forgot-password',
				'message' => Localization::getText($this->language, __METHOD__ . "-passwordRetypedFailed"),
			))
		));

		$passwordRetyped->setLabel(Localization::getText($this->language, __METHOD__ . "-passwordRetypedLabel"));

		$this->add($passwordRetyped);

		$token = new Hidden('forgot-resetToken');

		$this->add($token);

		$csrf = new Hidden('forgot-csrfToken');

		$csrf->addValidator(
			new Identical(array(
				'value' => $this->security->getSessionToken(),
				'message' => Localization::getText($this->language, __METHOD__ . "-csrfFailed"),
			))
		);

		$this->add($csrf);

		$this->add(new Submit('forgot-submit', array(
			'class' => 'btn btn-warning btn-lg btn-block submit',
			'id' => 'forgot-submit',
			'value' => Localization::getText($this->language, __METHOD__ . "-submitValue"),
			)
		));
	}
}