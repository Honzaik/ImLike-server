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

class LoginForm extends Form{

	private $language = "en";

	public function initialize(){
		$email = new EmailInput('login-email', array(
	    	'placeholder' => Localization::getText($this->language, __METHOD__ . "-emailPlaceholder"),
	    	'class' => 'form-control',
	    	'id' => 'login-email',
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

	    $password = new Password('login-password', array(
	    	'placeholder' => Localization::getText($this->language, __METHOD__ . "-passwordPlaceholder"),
	    	'class' => 'form-control',
	    	'id' => 'login-password',
	    	'maxlength' => '128',
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

	    $csrf = new Hidden('login-csrf');

	    $csrf->addValidator(new Identical(array(
	    	'value' => $this->security->getSessionToken(),
	    	'message' => Localization::getText($this->language, __METHOD__ . "-csrfFailed"),
	    )));

	    $this->add($csrf);

	    $this->add(new Submit('login-submit', array(
	    	'class' => 'btn btn-lg btn-block submit',
	    	'id' => 'login-submit',
	    	'value' => Localization::getText($this->language, __METHOD__ . "-submitValue"),
	    )));
	}
}