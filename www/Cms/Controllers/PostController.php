<?php

namespace CMS\Controller;
use App\Models\User;
use App\Models\Site;

use CMS\Models\Post;
use CMS\Models\Page;
use CMS\Models\Category;

use CMS\Core\View;
use CMS\Core\NavbarBuilder;

class PostController{


	public function defaultAction($site){
		$html = 'Default admin action on CMS <br>';
		$html .= 'We\'re gonna assume that you are the site owner <br>'; 
		$view = new View('admin', 'back');
		$view->assign("navbar", navbarBuilder::renderNavBar($site, 'back'));
		$view->assign('pageTitle', "Dashboard");
		$view->assign('content', $html);
	}

	public function createArticleAction($site){
		$postObj = new Post();

		$page = new Page();
		$page->setPrefix($site['prefix']);
		$pages = $page->findAll();
		$pagesArr = array();
		foreach($pages as $data){
			$pagesArr[$data['id']] = $data['name'];
		}

		$form = $postObj->formAddContent($pagesArr);

		if(!empty($_POST) ) {
			[ "title" => $title, "content" => $content ] = $_POST;
			if($title && $content){
				$insert = new Post();
				$insert->setTitle($title);
				$insert->setContent($content);
				$insert->setPublisher(2);
				$insert->setPrefix($site['prefix']);
				$adding = $insert->save();
				if($adding){
					$message ='Article successfully published!';
					$view->assign("message", $message);
				}else{
					$errors = ["Impossible d\'inserer l'article"];
					$view->assign("errors", $errors);
				}
			}
		}

		$view = new View('admin.create', 'back');
		$view->assign("navbar", navbarBuilder::renderNavBar($site, 'back'));
		$view->assign("form", $form);
		$view->assign('pageTitle', "Add an article");
	}

	public function manageArticlesAction($site){
		$postObj = new Post();
		$postObj->setPrefix($site['prefix']);
		$posts = $postObj->findAll();
		$postList = [];

		foreach($posts as $item){
			$userObj = new User();
			$userObj->setId($item['publisher']);
			$user = $userObj->findOne();

			$item['publisher'] = ("by " . $user['firstname'])??'None';
			$postList[] = $postObj->listFormalize($item);
		}
		$createArticleBtn = ['label' => 'Create an article', 'link' => 'createarticle'];

		$view = new View('admin.list', 'back');
		$view->assign("navbar", navbarBuilder::renderNavBar($site, 'back'));
		$view->assign("button", $createArticleBtn);
		$view->assign("list", $postList);
		$view->assign('pageTitle', "Manage the articles");
	}

	public function editArticleAction($site){
		if(!isset($_GET['id']) || empty($_GET['id']) ){
			echo 'article not set ';
		}

		$page = new Page();
		$page->setPrefix($site['prefix']);
		$pages = $page->findAll();
		$pagesArr = array();
		foreach($pages as $data){
			$pagesArr[$data['id']] = $data['name'];
		}

		$contentObj = new Post();
		$contentObj->setPrefix($site['prefix']);
		$contentObj->setId($_GET['id']);
		$content = $contentObj->findOne();
		if(!$content){
			header("Location: managearticles");
		}
		$pagesArr = array();
		foreach($pages as $data){
			$pagesArr[$data['id']] = $data['name'];
		}

		$form = $contentObj->formEditContent((array)$content, $pagesArr);


		if(!empty($_POST) ) {
			[ "title" => $title, "content" => $content] = $_POST;
			if($title && $content ){
				/*$insert = new Content($title, $content, $page, 2);
				$insert->setTableName($site['prefix']);*/
				$contentObj->setTitle($title);
				$contentObj->setContent($content);
				$adding = $contentObj->save();
				if($adding){
					$message ='Article successfully updated!';
					$view->assign("message", $message);
				}else{
					$errors = ["Error when updating this article"];
					$view->assign("errors", $errors);
				}
			}
		}

		$view = new View('admin.create', 'back');
		$view->assign("navbar", navbarBuilder::renderNavBar($site, 'back'));
		$view->assign("form", $form);
		$view->assign('pageTitle', "Edit an article");

	}

	/*
	* Front vizualization
	* returns html for pageRenderer
	*/
	public function renderList($site, $filter = null){
		$postObj = new Post();
        $postObj->setPrefix($site->getPrefix());
        $contents = $postObj->findAll();
        $html = "";
        if(!$contents || count($contents) === 0){
            $html .= 'No content found :/';
            return;
        }

        foreach($contents as $content){
            $postObj = new Post();
			$postObj->setTitle($content['title']);
			$postObj->setContent($content['content']);
			$postObj->setPublisher($content['publisher']);
			$postObj->setId($content['id']);
			$html .= $this->renderPostItem($postObj->returnData());
        }
		return $html;
	}

	public function renderPostItem($content){
        $publisherData = new User();
        extract($content);
		if(!empty($publisher))
        {
			$publisherData->setId($publisher);
        	$publisher = $publisherData->findOne();
			$name = $publisher['firstname'] . " " . $publisher['lastname'];
		}else{
			$name = 'Unknown';
		}
        
		$html = '<h2><a href="ent/post?id='. $id . '">' . $title . '</a></h2>';
		$html .= '<p id='. $publisher['id'] .' >By ' . $name . ' </p>';
		$html .= '<p>' . $content . '</p>';
		$html .= '<hr>';

        return $html;
	}

	//$site is an instance of Site
	public function renderPostAction($site){
		if(!isset($_GET['id']) || empty($_GET['id']) ){
			return 'article not set ';
		}

		$postObj = new Post();
        $postObj->setPrefix($site->getPrefix());
		$postObj->setId($_GET['id']);
        $post = $postObj->findOne();
        if(!$post){
            return 'No content found :/';
        }
        $publisherData = new User();
		if(!empty($publisher))
        {
			$publisherData->setId($publisher);
        	$publisher = $publisherData->findOne();
			$name = $publisher['firstname'] . " " . $publisher['lastname'];
		}else{
			$name = 'Unknown';
		}

        extract($post);

        $html = "";
		$html = '<h2>' . $title . '</h2>';
		$html .= '<p id='. $publisher['id'] .' >By ' . $name . ' </p>';
		$html .= '<p>' . $content . '</p>';
		$html .= '<hr>';
		$html .= '<p>Ajouter un commentaire</p>';

		return $html;
	}

}