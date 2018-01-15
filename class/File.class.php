<?php

include_once('Database.class.php');
include_once('../generic/randomstr.php');

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
	protected $tmp_path;

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
		$this->init_get = false;
		$this->init_post = false;
		return true;
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
			$this->url = RELATIVE_FILES_DIRECTORY.$data['F_PATH'];
			$this->size = $data['F_SIZE'];
			$this->type = $data['F_TYPE'];
			$this->init_get = true;
			return true;
		}

		$this->init = false;
		return false;
	}

	public function init_for_post($key, $folder = '')
	{
		if($this->init_get)
		{
			return false;
		}

		if($this->init_post)
		{
			return true;
		}

		else
		{
			if(!isset($_FILES[$key]))
			{
				return false;
			}

			do
			{
				$this->path = $folder.'/'.random_str(10);
			}
			while(file_exists(ABSOLUTE_FILES_DIRECTORY.$this->path));

			$this->name = '"'.addslashes($_FILES[$this->key]['name']).'"';
			$this->tmp_path = $_FILES[$this->key]['tmp_name'];
			$this->type = '"'.addslashes($_FILES[$this->key]['type']).'"';
			$this->size = (int)$_FILES[$this->key]['size'];
			$this->md5 = md5_file($_FILES[$this->key]['tmp_name']);
			return true;
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

	public function get()
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
		if(!$this->init_post)
		{
			return false;
		}

		$cursor = $this->database->req_post('CALL INSERT_FILE(:name, :path, :size, :type, :md5);',
			array(
				'name' => $this->name,
				'path' => $this->path,
				'size' => $this->size,
				'type' => $this->type,
				'md5' => $this->md5
			)
		);

		if($data = $cursor->fetch())
		{
			if(!move_uploaded_file($this->tmp_path,$this->path))
			{
				$this->database->req_post('CALL DELETE_FILE(:id);',
					array(
						'id' => $data['F_UID']
					)
				);
				return false;

			}
			else
			{
				$this->reset_file();
				$this->id = $data['F_UID'];
				$this->name = $data['F_NAME'];
				$this->path = $data['F_PATH'];
				$this->url = RELATIVE_FILES_DIRECTORY.$data['F_PATH'];
				$this->size = $data['F_SIZE'];
				$this->type = $data['F_TYPE'];
				$this->init_get = true;
				return true;
			}
		}
		else
		{
			return false;
		}
	}

	public function update($key, $folder)
	{
		if(!$this->init_get)
		{
			return false;
		}

		if($this->delete())
			if($this->reset_file())
				if($this->init_for_post($key, $folder))
					if($this->post())
						return true;

		return false;
	}

	public function delete()
	{
		if(!$this->init_get)
		{
			return false;
		}

		$this->database->req_post('CALL DELETE_FILE(:id);',
			array(
				'id' => $this->id
			)
		);
		unlink(ABSOLUTE_FILES_DIRECTORY.$this->path);

		$this->reset_file();
		return true;
	}
}
