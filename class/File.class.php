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
	protected $key;

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
	protected $init_get;

	/**
	 * @var bool
	 */
	protected $init_post;

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
	public function reset_file()
	{
		$this->init = false;
	}

	public function init_for_get($id)
	{

		if($this->init_post)
		{
			return false;
		}

		if($this->init_get)
		{
			return true;
		}

		$req = $this->database->req_get('CALL SELECT_FILE('.$id.');');
		$data = $req->fetch();
		$req->closeCursor();

		if($data)
		{
			$this->id = $data['F_UID'];
			$this->name = $data['F_NAME'];
			$this->path = $data['F_PATH'];
			$this->url = $data['F_PATH'];
			$this->size = $data['F_SIZE'];
			$this->type = $data['F_TYPE'];
			$this->init_get = true;
			return true;
		}

		$this->init = false;
		return false;
	}

	public function init_for_post($key, $path)
	{
		if($this->init_get) {
			return false;
		}

		if($this->init_post)
		{
			return true;
		}

		else
		{
			//TODO: content
			return false;
		}

	}

	public function get_url()
	{
		if($this->init_get)
		{
			return $this->url;
		}
		else
		{
			return false;
		}
	}

	public function get_info()
	{
		if($this->init_get)
		{
			return array(
				'STATUS' => 'GET',
				'ID' => $this->id,
				'URL' => $this->url,
				'PATH' => $this->path,
				'NAME' => $this->name,
				'TYPE' => $this->type,
				'SIZE' => $this->size,
				'MD5' => $this->md5
			);
		}
		else if($this->init_post)
		{
			return array(
				'STATUS' => 'POST',
				'NAME' => $_FILES[$this->key]['name'],
				'TMP_PATH' => $_FILES[$this->key]['tmp_name'],
				'TYPE' => $_FILES[$this->key]['type'],
				'SIZE' => $_FILES[$this->key]['size'],
				'MD5' => md5_file($_FILES[$this->key]['tmp_name'])
			);
		}
		else
		{
			return false;
		}
	}

	public function post()
	{

	}

	public function update()
	{

	}

	public function file_exist()
	{

	}

	public function delete_file()
	{

	}

	public function upload_file()
	{

	}

	public function delete()
	{

	}
}
