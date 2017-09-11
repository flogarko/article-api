<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Article;

class ArticleController extends Controller
{
    /**
     * @Get(
     *		path = "/articles/{id}",
     *		name = "app_article_show",
     *		requirements = {"id"="\d+"}
     *	)
     *	@View
     */
    public function showAction()
    {
    	$article = new Article();
    	$article->setTitle('Mon Titre');
    	$article->setContent('Mon Contenu');
    	
    	return $article;
       
    }
}
