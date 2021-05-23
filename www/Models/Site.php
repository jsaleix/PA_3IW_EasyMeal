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
            $insert = new Page('home', $this->prefix);
            $insert->save();
            echo 'Page created';

            $contentObj = new Content();
            $contentObj->setTableName($this->prefix);
            $contentObj->setPage(1);
            $contentObj->setMethod(1);
            $contentObj->save();
            echo 'Content created';

            $postObj = new Post('Welcome', 'This is your first article on your new website.', 2);
            $postObj->setTableName($this->prefix);
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

}




