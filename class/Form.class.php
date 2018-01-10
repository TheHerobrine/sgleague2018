<?php

include_once('File.class.php');
include_once('Database.class.php');

/**
 * Form class, used to check data before to sens to database
 *
 * it's based on arrays $_POST
 *
 * @example SQL_management/examples.php 3 31  "Form example of use"
 * @package SQL\Form
 * @tags Form
 */
class Form {

	/**
	 * uses to communicate with database
	 * @var Database $database
	 */
	protected $database;

	/**
	 * form's fields definition and use
	 * @var array[] $fields
	 */
	protected $fields = array();

	/**
	 * Values to send with query
	 * @var array $values
	 */
	protected $values = array();

	/**
	 * sql procedure to send form
	 * @var string $sql
	 */
	protected $sql = NULL;

	/**
	 * validation of Form
	 * @var bool $valid
	 */
	protected $valid = false;

	/**
	 * Information about validation
	 * @var string $unvalidated_message
	 */
	public $unvalidated_message;

	/**
	 * Error code for validation
	 * @var integer $unvalidated_code
	 */
	public $unvalidated_code;

	public function __construct(Database &$bdd, $query, array $fields_array)  {

		if(empty($fields_array) OR !is_array($fields_array)) {
			throw new Exception('$fields_array is not an array');
		}

		$this->fields = &$fields_array;
		$this->database = &$bdd;
		$this->sql = $query;
	}

	/**
	 * valid form's fields contained in $_POST
	 * @return bool
	 */
	public function is_valid() {

		if($this->valid) {
			return $this->valid;
		}

		foreach ($this->fields as $key => $field) {

			if(isset($_POST[$key])) {

				switch ($field['type']) {

					case 'integer':
						$this->values[$key]=(int)$_POST[$key];
						break;

					case 'float':
						$this->values[$key]=(float)$_POST[$key];
						break;

					case 'string':
						if(isset($field['Fregex']) AND preg_match($field['Fregex'],$_POST[$key])){
							$this->unvalidated_message = $key . ' : Regex : "' . $field['Fregex'] . '" matched : "' . $_POST[$key] . '"';
							$this->unvalidated_code = 11;
							$this->valid = false;
							return false;
						}
						if(isset($field['Tregex']) AND !preg_match($field['Tregex'],$_POST[$key])){
							$this->unvalidated_message = $key . ' : Regex : "' . $field['Tregex'] . '" didn\'t match : "' . $_POST[$key] . '"';
							$this->unvalidated_code = 12;
							$this->valid = false;
							return false;
						}
						$str = '"'.addslashes(substr($_POST[$key],0,$field['length'])).'"';
						if(isset($field['length']) AND strlen($str) > $field['length']){
							$this->unvalidated_message = $key . ' : String is too long : ' . $str;
							$this->unvalidated_code = 10;
							$this->valid = false;
							return false;
						}
						$this->values[$key]=$str;
						break;

					case 'date':
						if(!preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/",htmlspecialchars($_POST[$key]))) {
							$this->unvalidated_message = $key . ' : Date is not valid : ' . $_POST[$key];
							$this->unvalidated_code = 20;
							$this->valid = false;
							return false;
						} else {
							$this->values[$key]=$_POST[$key];
						}
						break;

					case 'datetime':
						if(!preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/",htmlspecialchars($_POST[$key]))) {
							$this->unvalidated_message = $key . ' : Datetime is not valid : ' . $_POST[$key];
							$this->unvalidated_code = 21;
							$this->valid = false;
							return false;
						} else {
							$this->values[$key]=$_POST[$key];
						}
						break;

					case 'mail':
						if(!filter_var($_POST[$key], FILTER_VALIDATE_EMAIL)){
							$this->unvalidated_message = $key . ' : Mail is not valid : ' . $_POST[$key];
							$this->unvalidated_code = 30;
							$this->valid = false;
							return false;
						} else {
							$this->values[$key]=$_POST[$key];
						}
						break;
				}
			} else {
				$this->unvalidated_message = $key . ' : Is missing';
				$this->unvalidated_code = 40;
				$this->valid = false;
				return false;
			}
		}

		$this->valid = true;
		return $this->valid;
	}

	/**
	 * check if form is_valid and then send request
	 * @return bool|req the request's result or false
	 */
	public function send()
	{
		if($this->is_valid()) {
			return $this->database->req_post($this->sql, $this->values);
		} else {
			return false;
		}
	}
}