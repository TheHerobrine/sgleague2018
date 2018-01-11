<?php

include_once('Database.class.php');

class File
{

	/**
	 * @var string
	 */
	static protected $rootPath;

	/**
	 * @var Database
	 */
	protected $database;

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $md5;

	/**
	 * KB
	 * @var integer
	 */
	protected $size;

	/**
	 * @var bool
	 */
	protected $init;

	/**
	 * File constructor.
	 * @param Database $bdd
	 */
	public function __construct(Database &$bdd)
	{
		$this->database = $bdd;
	}

	/**
	 * Reset file information
	 */
	public function reset_file() {
		$this->init = false;
	}

	public function init_for_get($id) {

		if($this->init == true) {
			return true;
		}

		$req = $this->database->req_get('CALL SELECT_FILE('.$id.');');
		if($data = $req->fetch()){
			$this->id = $data['F_UID'];
			$this->name = $data['F_NAME'];
			$this->path = $data['F_PATH'];
			$this->url = $data['F_PATH'];
			$this->size = $data['F_SIZE'];
			$this->type = $data['F_TYPE'];
			$this->init = true;
			return true;
		}

		$this->init = false;
		return false;
	}

	public function init_for_post($key) {

		if($this->init ==true) {
			return true;
		} else {
			//TODO: content
			return false;
		}

	}

	public function get_url() {

	}

	public function get_info() {

	}

	public function post() {

	}

	public function update() {

	}

	public function file_exist() {

	}

	public function delete_file()
	{

	}

	public function upload_file()
	{

	}

	public function delete() {

	}
}
