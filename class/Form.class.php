<?php

include_once('Database.class.php');
include_once('File.class.php');

define ("METHOD_DEFAULT", 0);
define ("METHOD_POST", 1);
define ("METHOD_GET", 2);

/**
 * Exmple d'utilisation
 * $types_for_file = array(image_type_to_mime_type(IMAGETYPE_JPEG), image_type_to_mime_type(IMAGETYPE_PNG));
 * $fields = array(
 * 	'field1' => array('type' => 'integer'),
 * 	'field2' => array('type' => 'string', 'length' => '15', 'Tregex' => 'doiefjzoiefj'),
 * 	'field3' => array('type' => 'mail'),
 *  'field4' => array('type' => 'value', 'value' => 'patate'),
 *  'field5' => array('type' => 'file', 'types' =>  $types_for_file, 'destination' => '\\', 'max_size' => 5000000, 'max_width' => 800, 'max_height' => 800)
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
	 * @var bool $method
	 */
	protected $method = 0;

	/**
	 * to delete files if abort
	 * @var array $file_control
	 */
	protected $file_control = array();

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

	public function __construct(Database &$bdd, $query, array $fields_array, $method = METHOD_POST)
	{
		if(empty($fields_array) OR !is_array($fields_array))
		{
			throw new Exception('$fields_array is not an array');
		}

		$this->fields = &$fields_array;
		$this->database = &$bdd;
		$this->sql = $query;

		if ($method == METHOD_POST)
		{
			$this->method = &$_POST;
		}
		else if ($method == METHOD_GET)
		{
			$this->method = &$_GET;
		}
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

		$this->valid = true;

		foreach ($this->fields as $key => $field)
		{

			if(isset($this->method[$key]) || ($field['type'] == "value") || ($field['type'] == "file"))
			{

				switch ($field['type'])
				{

					case 'integer':
						$this->values[$key]=(int)$this->method[$key];
						break;

					case 'float':
						$this->values[$key]=(float)$this->method[$key];
						break;

					case 'string':
						if(isset($field['Fregex']) AND preg_match($field['Fregex'],$this->method[$key]))
						{
							$this->unvalidated_message = $key . ' : Regex : "' . $field['Fregex'] . '" matched : "' . $this->method[$key] . '"';
							$this->unvalidated_code = 11;
							$this->valid = false;
						}
						if(isset($field['Tregex']) AND !preg_match($field['Tregex'],$this->method[$key]))
						{
							$this->unvalidated_message = $key . ' : Regex : "' . $field['Tregex'] . '" didn\'t match : "' . $this->method[$key] . '"';
							$this->unvalidated_code = 12;
							$this->valid = false;
						}
						if(isset($field['length']) AND strlen($this->method[$key]) > $field['length'])
						{
							$this->unvalidated_message = $key . ' : String is too long : ' . $this->method[$key];
							$this->unvalidated_code = 10;
							$this->valid = false;
						}
						$this->values[$key] = $this->method[$key];
						break;

					case 'date':
						if(!preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/",htmlspecialchars($this->method[$key])))
						{
							$this->unvalidated_message = $key . ' : Date is not valid : ' . $this->method[$key];
							$this->unvalidated_code = 20;
							$this->valid = false;
						}
						else
						{
							$this->values[$key]=$this->method[$key];
						}
						break;

					case 'datetime':
						if(!preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/",htmlspecialchars($this->method[$key])))
						{
							$this->unvalidated_message = $key . ' : Datetime is not valid : ' . $this->method[$key];
							$this->unvalidated_code = 21;
							$this->valid = false;
						}
						else
						{
							$this->values[$key]=$this->method[$key];
						}
						break;

					case 'mail':
						if(!filter_var($this->method[$key], FILTER_VALIDATE_EMAIL)){
							$this->unvalidated_message = $key . ' : Mail is not valid : ' . $this->method[$key];
							$this->unvalidated_code = 30;
							$this->valid = false;
						}
						else
						{
							$this->values[$key]=$this->method[$key];
						}
						break;

					case 'value':
						if(!$field['value'])
						{
							$this->unvalidated_message = $key . ' : Value is not set';
							$this->unvalidated_code = 42;
							$this->valid = false;
						}
						else
						{
							$this->values[$key] = $field['value'];
						}
						break;

					case 'file':
						if(!isset($_FILES[$key]))
						{
							$this->unvalidated_message = $key . ' : File doesn\'t exist';
							$this->unvalidated_code = 54;
							$this->valid = false;
						}
						else if(!isset($field['destination']))
						{
							$this->unvalidated_message = $key . ' : Destination not set';
							$this->unvalidated_code = 55;
							$this->valid = false;
						}
						else if(!isset($field['types']))
						{
							$this->unvalidated_message = $key . ' : Authorized types not set';
							$this->unvalidated_code = 53;
							$this->valid = false;
						}
						else
						{
							include_once('File.class.php');

							$file = new File($this->database);
							$maxS = (isset($field['max_size']))? $field['max_size']: 5000000;
							$maxW = (isset($field['max_width']))? $field['max_width']: 800;
							$maxH = (isset($field['max_height']))? $field['max_height']: 800;
							if(!$file->init_for_post($key, $field['destination'], $field['types'], $maxS, $maxW, $maxH))
							{
								$this->unvalidated_message = $key . ' : Error during file initialization';
								$this->unvalidated_code = 51;
								$this->valid = false;
							}
							else
							{
								if(!$file->post())
								{
									$this->unvalidated_message = $key . ' : Error during file uploading';
									$this->unvalidated_code = 52;
									$this->valid = false;
								}
								else
								{
									$file_info = $file->get();
									$this->values[$key] = $file_info['ID'];
									$this->file_control[$key] = $file;
								}
							}
						}
						break;

					default:
						$this->unvalidated_message = $key . ' : Is not valid type';
						$this->unvalidated_code = 41;
						$this->valid = false;
						break;
				}
			}/* else {
				$this->unvalidated_message = $key . ' : Is missing';
				$this->unvalidated_code = 40;
				$this->valid = false;
				return false;
			}*/
		}

		debug("SQL VALID(".($this->valid?"VALID":"ERROR").")",$this->values);
		
		return $this->valid;
	}

	/**
	 * check if form is_valid and then send request
	 * @return bool|req the request's result or false
	 */
	public function send()
	{
		if($this->is_valid())
		{
			try
			{
				return $this->database->req_post($this->sql, $this->values);
			}
			catch (Exception $e)
			{
				foreach($this->file_control as $key => $file)
				{
					$file->delete();
				}
				$this->unvalidated_message = 'Error to execute request';
				$this->unvalidated_code = 60;
				return false;
			}
		}
		else
		{
			debug("SQL SEND()",$this->sql.'<br/>Error '.($this->unvalidated_code).' : '.$this->unvalidated_message);
			return false;
		}
	}
}