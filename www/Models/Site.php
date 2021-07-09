<?php

namespace App\Models;

use App\Core\Database;
use App\Core\FileUploader;
use App\Core\Security;

use CMS\Models\Page;
use CMS\Models\Post;
use CMS\Models\DishCategory;


class Site extends Model
{

	protected $id = null;
	protected $name;
	protected $description;
	protected $image;
	protected $creator;
	protected $subDomain;
	protected $prefix;
    protected $type;
    protected $theme;

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
    

    public function getTheme()
    {
        return $this->theme;
    }

    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    public function setName($name)
    {
        $this->name = htmlspecialchars($name);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDescription($description)
    {
        $this->description = htmlspecialchars($description);
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
        $this->subDomain = trim(mb_strtolower($subDomain));
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
        $this->type = htmlspecialchars($type);
    }

    public function initializeSite(){
        if(!$this->name){ throw new \InvalidArgumentException("missing fields"); }
        if($this->id){ throw new \InvalidArgumentException("The site already exists"); }
        if(!($this->save())){ return false; }


        // Creation of new tables 
        $dir = basename(__DIR__) . '/../Assets/scripts';
        clearstatcache();
        $sqlFiles = array(
            'dish_category', 'dish', 'booking', 'booking_settings', 'booking_planning', 'category', 'page', 'medium', 'post', 'content', 'comment', 'menu', 'menu_dish_association', 'post_medium_association'
        );

        foreach($sqlFiles as $file)
        {
            if(!file_exists($dir . '/' . $file .'.script' )){
                die("Missing required file " . $file);
                return false;
            }
        }

        $toReplace = [':X', ':prefix'];
        $replaceBy = [$this->prefix, DBPREFIXE];

        try{
            foreach( $sqlFiles as $table){
                $table = file_get_contents($dir . '/'.$table.'.script');
                $script = str_replace($toReplace, $replaceBy, $table);
                $create = $this->createTable($script);
                if(!$create){ echo $table; return false; }
            }
            $insert = new Page();
            $insert->setName('home');
            $insert->setPrefix($this->prefix);
            $insert->setCreator(Security::getUser());
            $insert->save();

            FileUploader::createCMSDirs($this->subDomain);

            $postObj = new Post($this->prefix);
            $postObj->setTitle('Welcome');
            $postObj->setContent('This is your first article on your new website.');
            $postObj->setPublisher(Security::getUser());
            $postObj->save();

            $dishCatObj = new DishCategory($this->prefix);
            $dishCatArr = [ 'Starters', 'Dishes', 'Desserts', 'Drinks'];
            foreach($dishCatArr as $cat){
                $dishCatObj->setName($cat);
                $dishCatObj->save();
            }

            return true;
        }catch(\Exception $e){
            return false;
        }
    }

    public function returnData() : array{
		return get_object_vars($this);
	}


    public function formThemeEdit($themes){
        return [
            "config"=>[
                "method"=>"POST",
                "action"=>"",
                "class"=>"col-10",
                "submit"=>"Change theme",
                "submitClass"=>"btn btn-100 btn-light"
            ],
            "inputs"=>[
                "theme"=>[
                    "type"=>"select",
                    "class"=>"input input-100 input-select",
                    "options"=>$themes
                ]
            ]
        ];
    }

    public function formEdit($content){
        return [

            "config"=>[
                "method"=>"POST",
                "action"=>"",
                "id"=>"form_content",
                "class"=>"edit-site col-5 col-sm-12",
                "submit"=>"Apply",
                "submitClass"=>"cta-blue width-80 last-sm-elem",
                "enctype"=>"multipart/form-data",
            ],
            "inputs"=>[
                "name"=>[ 
                    "type"=>"text",
                    "label"=>"Name",
                    "minLength"=>2,
                    "maxLength"=>45,
                    "id"=>"name",
                    "class"=>"input input-100",
                    "placeholder"=>"Website name",
                    "error"=>"The name cannot be empty!",
                    "required"=>true,
					"value"=> $content['name']
                ],
				"description"=>[ 
					"type"=>"text",
					"placeholder"=>"Description",
					"id"=>"description",
					"class"=>"input input-100",
                    "error"=>"The description cannot be empty!",
					"required"=> false,
					"value"=> $content['description']
                ],
                "type"=>[ 
					"type"=>"text",
					"label"=>"type",
					"id"=>"type",
					"class"=>"input input-100",
                    "placeholder"=>"Restaurant food type",
                    "error"=>"The type cannot be empty!",
					"required"=> false,
					"value"=> $content['type'],
                ],
				"image"=>[ 
					"type"=>"file-img",
					"label"=>"New banner",
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
					"class"=>"input input-100",
                    "error"=>"The subDomain cannot be empty!",
					"required"=> false,
					"value"=> $content['subDomain'],
                    "disabled" => true
                ],
                "creationDate"=>[ 
					"type"=>"text",
					"label"=>"creationDate",
					"id"=>"creationDate",
                    "placeholder"=>"Creation date",
					"class"=>"input input-100",
                    "error"=>"The creationDate cannot be empty!",
					"required"=> false,
					"value"=> $content['creationDate'],
                    "disabled" => true
                ],
            ]
        ];
    }
    

}




