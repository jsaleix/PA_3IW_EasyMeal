<?php

namespace App\Models;

use App\Core\Database;

use CMS\Models\Page;
use CMS\Models\Content;
use CMS\Models\Post;

class Site extends Database
{

	protected $id = null;
	protected $name;
	protected $description;
	protected $image;
	protected $creator;
	protected $subDomain;
	protected $prefix;
    protected $type;

	public function __construct(){
		parent::__construct();
	}

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getCreator()
    {
        return $this->creator;
    }

    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    public function getSubDomain()
    {
        return $this->subDomain;
    }

    public function setSubDomain($subDomain)
    {
        $this->subDomain = $subDomain;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function initializeSite(){
        if(!$this->name){ throw new InvalidArgumentException("missing fields"); }
        if($this->id){ throw new InvalidArgumentException("The site already exists"); }
        if(!($this->save())){ return false; }
        // Creation of new tables 
        $dir = basename(__DIR__) . '/../Assets/scripts';

        clearstatcache();
        if( !file_exists($dir . '/booking.script') || !file_exists($dir . '/category.script') || !file_exists($dir . '/content.script') || !file_exists($dir . '/dish_category.script') ||
            !file_exists($dir . '/dish.script') || !file_exists($dir . '/medium.script') || !file_exists($dir . '/page.script') || !file_exists($dir . '/post.script') )
        {
			die("Missing required file");
            return false;
		}

        $toReplace = [':X', ':prefix'];
        $replaceBy = [$this->prefix, DBPREFIXE];
        $tableToCreate = [ '/dish_category.script', '/dish.script', '/booking.script', '/category.script', '/page.script', '/medium.script', '/post.script', '/content.script'];
        try{
            foreach( $tableToCreate as $table){
                $table = file_get_contents($dir . $table);
                $create = $this->createTable(str_replace($toReplace, $replaceBy, $table));
                if(!$create){ echo $table; return false; }
            }
            $insert = new Page();
            $insert->setName('home');
            $insert->setPrefix($this->prefix);
            $insert->save();
            echo 'Page created';

            $contentObj = new Content();
            $contentObj->setPrefix($this->prefix);
            $contentObj->setPage(1);
            $contentObj->setMethod(1);
            $contentObj->save();
            echo 'Content created';

            $postObj = new Post();
            $postObj->setTitle('Welcome');
            $postObj->setContent('This is your first article on your new website.');
            $postObj->setPublisher(2);
            $postObj->setPrefix($this->prefix);
            $postObj->save();
            echo 'Post created';

            /*$insert = new Content('Welcome', 'This is your first article on your new website.', 1, 2);
            $insert->setTableName($this->prefix);
            $insert->save();
            echo 'Content created';*/

            return true;
        }catch(\Exception $e){
            return false;
        }
    }

    public function returnData() : array{
		return get_object_vars($this);
	}

    public function formEdit($content){
        return [

            "config"=>[
                "method"=>"POST",
                "action"=>"",
                "id"=>"form_content",
                "class"=>"form-content",
                "submit"=>"Apply",
                "submitClass"=>"cta-blue width-80 last-sm-elem"
            ],
            "inputs"=>[
                "name"=>[ 
                    "type"=>"text",
                    "label"=>"Name",
                    "minLength"=>2,
                    "maxLength"=>45,
                    "id"=>"name",
                    "class"=>"input-content",
                    "placeholder"=>"New article",
                    "error"=>"The name cannot be empty!",
                    "required"=>true,
					"value"=> $content['name']
                ],
				"description"=>[ 
					"type"=>"text",
					"label"=>"Description",
					"id"=>"description",
					"class"=>"input-content",
                    "error"=>"The description cannot be empty!",
					"required"=> false,
					"value"=> $content['description']
                ],
				"image"=>[ 
					"type"=>"file",
					"label"=>"image",
					"id"=>"image",
					"class"=>"input-file",
                    "error"=>"",
					"required"=> false,
					"value"=> $content['image']
                ],
                "subDomain"=>[ 
					"type"=>"text",
					"label"=>"subDomain",
					"id"=>"subDomain",
					"class"=>"input-content",
                    "error"=>"The subDomain cannot be empty!",
					"required"=> false,
					"value"=> $content['subDomain'],
                    "disabled" => true
                ],
                "type"=>[ 
					"type"=>"text",
					"label"=>"type",
					"id"=>"type",
					"class"=>"input-content",
                    "error"=>"The type cannot be empty!",
					"required"=> false,
					"value"=> $content['type'],
                ],
                "creationDate"=>[ 
					"type"=>"text",
					"label"=>"creationDate",
					"id"=>"creationDate",
					"class"=>"input-content",
                    "error"=>"The creationDate cannot be empty!",
					"required"=> false,
					"value"=> $content['creationDate'],
                    "disabled" => true
                ],
            ]
        ];
    }

}




