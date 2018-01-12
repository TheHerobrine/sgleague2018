<?php

include_once('File.class.php');
include_once('Database.class.php');

/**
 * Exmple d'utilisation
 * $fields = array(
 * 	'field1' => array('type' => 'integer'),
 * 	'field2' => array('type' => 'string', 'length' => '15', 'Tregex' => 'doiefjzoiefj'),
 * 	'field3' => array('type' => 'mail'),
 *  'field4' => array('type' => 'value', 'value' => 'patate')
 *
 * );
 * $query = "CALL MA_QUERY(:field1, :field2, :field3, :field4)";
 *
 * $form = new Form(new Database(), $query, $fields);
 * if($form->is_valid()){
 * 	$result = $form->send();
 * } else {
 *	$error_code = $form->unvalidated_code;
 * 	// 1[0-2] - 'string pas valid'
 * 	// 2[0-1] - 'date ou datetime pas valid'
 * 	// 3[0] - 'mail pas valid'
 *	// 4[0-3] - Field missing or not valid
 * }
 */


/**
 * Form class, used to check data before to sens to database
 *
 * it's based on arrays $_POST
 *
 * @example SQL_management/examples.php 3 31  "Form example of use"
 * @package SQL\Form
 * @tags Form
 */
class Form
{
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
	 * post method used
	 * @var bool $post
	 */
	protected $post = true;

	/**
	 * debug mode
	 * @var bool $debug
	 */
	protected $debug = false;

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

	public function __construct(Database &$bdd, $query, array $fields_array, bool $post = true, bool $debug = false) 
	{
		if(empty($fields_array) OR !is_array($fields_array))
		{
			throw new Exception('$fields_array is not an array');
		}

		$this->fields = &$fields_array;
		$this->database = &$bdd;
		$this->sql = $query;
		$this->debug = $debug;
	}

	/**
	 * valid form's fields contained in $_POST
	 * @return bool
	 */
	public function is_valid()
	{
		if($this->valid) {
			return $this->valid;
		}

		if ($this->post)
		{
			$method = $_POST;
		}
		else
		{
			$method = $_GET;
		}

		foreach ($this->fields as $key => $field)
		{

			if(isset($method[$key]) || ($field['type'] == "value"))
			{

				switch ($field['type'])
				{

					case 'integer':
						$this->values[$key]=(int)$method[$key];
						break;

					case 'float':
						$this->values[$key]=(float)$method[$key];
						break;

					case 'string':
						if(isset($field['Fregex']) AND preg_match($field['Fregex'],$method[$key]))
						{
							$this->unvalidated_message = $key . ' : Regex : "' . $field['Fregex'] . '" matched : "' . $method[$key] . '"';
							$this->unvalidated_code = 11;
							$this->valid = false;
							return false;
						}
						if(isset($field['Tregex']) AND !preg_match($field['Tregex'],$method[$key]))
						{
							$this->unvalidated_message = $key . ' : Regex : "' . $field['Tregex'] . '" didn\'t match : "' . $method[$key] . '"';
							$this->unvalidated_code = 12;
							$this->valid = false;
							return false;
						}
						if(isset($field['length']) AND strlen($method[$key]) > $field['length'])
						{
							$this->unvalidated_message = $key . ' : String is too long : ' . $method[$key];
							$this->unvalidated_code = 10;
							$this->valid = false;
							return false;
						}
						$this->values[$key] = $method[$key];
						break;

					case 'date':
						if(!preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/",htmlspecialchars($method[$key])))
						{
							$this->unvalidated_message = $key . ' : Date is not valid : ' . $method[$key];
							$this->unvalidated_code = 20;
							$this->valid = false;
							return false;
						}
						else
						{
							$this->values[$key]=$method[$key];
						}
						break;

					case 'datetime':
						if(!preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/",htmlspecialchars($method[$key])))
						{
							$this->unvalidated_message = $key . ' : Datetime is not valid : ' . $method[$key];
							$this->unvalidated_code = 21;
							$this->valid = false;
							return false;
						}
						else
						{
							$this->values[$key]=$method[$key];
						}
						break;

					case 'mail':
						if(!filter_var($method[$key], FILTER_VALIDATE_EMAIL)){
							$this->unvalidated_message = $key . ' : Mail is not valid : ' . $method[$key];
							$this->unvalidated_code = 30;
							$this->valid = false;
							return false;
						}
						else
						{
							$this->values[$key]=$method[$key];
						}
						break;

					case 'value':
						if(!$field['value'])
						{
							$this->unvalidated_message = $key . ' : Value is not set : ';
							$this->unvalidated_code = 42;
							$this->valid = false;
							return false;
						}
						$this->values[$key] = $field['value'];
						break;

					default:
						$this->unvalidated_message = $key . ' : Is not valid type';
						$this->unvalidated_code = 41;
						$this->valid = false;
						return false;
						break;
				}
			}/* else {
				$this->unvalidated_message = $key . ' : Is missing';
				$this->unvalidated_code = 40;
				$this->valid = false;
				return false;
			}*/
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
		if ($this->debug)
		{
			echo '<div class="debug"><b>SEND()</b><br />'.$this->sql.'<br /><pre>';
			print_r($this->values);
			echo '</pre>';
		}

		if($this->is_valid())
		{
			if ($this->debug)
			{
				echo '</div>';
			}
			return $this->database->req_post($this->sql, $this->values);
		}
		else
		{
			if ($this->debug)
			{
				echo 'Error '.($this->unvalidated_code).' : '.$this->unvalidated_message.'</div>';
			}
			return false;
		}
	}
}