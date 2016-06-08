<?php
namespace ImLike\Forms;

use ImLike\Localization;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Email as EmailInput;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\Regex as RegexValidator;

class RegisterForm extends Form{

	private $language = "en";

	public function initialize(){
		$username = new Text('register-username', array(
			'placeholder' => Localization::getText($this->language, __METHOD__ . "-usernamePlaceholder"),
			'class' => 'form-control',
			'id' => 'register-username',
			'maxlength' => '60',
		));

		$username->addValidators(array(
			new StringLength(array(
				'max' => 60,
				'min' => 1,
				'messageMaximum' => Localization::getText($this->language, __METHOD__ . "-usernameMessageMax"),
				'messageMinimum' => Localization::getText($this->language, __METHOD__ . "-usernameMessageMin"),
			)),
			new RegexValidator(array(
			   'pattern' => '/^[a-zA-Z0-9_]+$/',
			   'message' => Localization::getText($this->language, __METHOD__ . "-usernameRegexMatch"),
			))
		));

		$username->setLabel(Localization::getText($this->language, __METHOD__ . "-usernameLabel"));

		$this->add($username);

		$email = new EmailInput('register-email', array(
			'placeholder' => Localization::getText($this->language, __METHOD__ . "-emailPlaceholder"),
			'class' => 'form-control',
			'id' => 'register-email',
			'maxlength' => '255',
			
		));

		$email->addValidators(array(
			new Email(array(
				'message' => Localization::getText($this->language, __METHOD__ . "-emailWrongFormat"),
			)),
			new StringLength(array(
				'max' => 255,
				'min' => 3,
				'messageMaximum' => Localization::getText($this->language, __METHOD__ . "-emailMessageMax"),
				'messageMinimum' => Localization::getText($this->language, __METHOD__ . "-emailMessageMin"),
			))
		));

		$email->setLabel(Localization::getText($this->language, __METHOD__ . "-emailLabel"));

		$this->add($email);

		$password = new Password('register-password', array(
			'placeholder' => Localization::getText($this->language, __METHOD__ . "-passwordPlaceholder"),
			'class' => 'form-control',
			'id' => 'register-password',
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

		$passwordRetyped = new Password('register-password-retyped', array(
			'placeholder' => Localization::getText($this->language, __METHOD__ . "-passwordRetypedPlaceholder"),
			'class' => 'form-control',
			'id' => 'register-password-retyped',
			'maxlength' => '128'
		));

		$passwordRetyped->addValidators(array(
			new Confirmation(array(
				'with'   => 'register-password',
				'message' => Localization::getText($this->language, __METHOD__ . "-passwordRetypedFailed"),
			))
		));

		$passwordRetyped->setLabel(Localization::getText($this->language, __METHOD__ . "-passwordRetypedLabel"));

		$this->add($passwordRetyped);

		$csrf = new Hidden('register-csrf');

		$csrf->addValidator(
			new Identical(array(
				'value' => $this->security->getSessionToken(),
				'message' => Localization::getText($this->language, __METHOD__ . "-csrfFailed"),
			))
		);

		$this->add($csrf);

		$this->add(new Submit('register-submit', array(
			'class' => 'btn btn-lg btn-block submit',
			'id' => 'register-submit',
			'value' => Localization::getText($this->language, __METHOD__ . "-submitValue"),
			)
		));
	}
}