<?php

namespace CMS\Models;
use App\Core\Database;

class Content extends Database
{

	protected $id = null;
	protected $page;
	protected $method;
    protected $filter;

	public function __construct(){
        parent::__construct();
	}

    public function setTableName($prefix){
		parent::setTableName($prefix.'_');
	}

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        // double action de peupler l'objet avec ce qu'il y a en bdd
        // https://www.php.net/manual/fr/pdostatement.fetch.php
    }

    public function getPage()
    {
        return $this->page;
    }


    public function setPage($page)
    {
        $this->page = $page;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }
}




