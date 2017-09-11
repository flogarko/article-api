<?php

namespace AppBundle\Controller;

use AppBundle\Representation\Articles;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Article;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use AppBundle\Exception\ResourceValidationException;

class ArticleController extends FOSRestController
{
    /**
     * @Get(
     *		path = "/articles/{id}",
     *		name = "app_article_show",
     *		requirements = {"id"="\d+"}
     *	)
     *	@View
     */
    public function showAction(Article $article)
    {
    	/*$article = new Article();
    	$article->setTitle('Mon Titre');
    	$article->setContent('Mon Contenu');*/
    	
    	return $article;
       
    }
    
    /**
     * @Post(
     *		path = "/articles",
     *		name = "app_article_create"
     *	 )
	 * @View(StatusCode=201)
	 * @ParamConverter("article",
     *     converter="fos_rest.request_body",
     *     options={
     *         "validator"={ "groups"="Create" }
     *     }
     * )
	 */
	function createAction(Article $article, ConstraintViolationList $violations)
	{

		if(count($violations))
        {
            $message = " The Json Contains Invalid data";
            foreach ($violations as $violation)
            {
                $message .= sprintf(
                    "Field %s: %s",
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                );

            }
            throw new ResourceValidationException($message);
        }

		$em = $this->getDoctrine()->getManager();
		$em->persist($article);
		$em->flush();
		
		return $this->view(
			$article, Response::HTTP_CREATED,
			[
				'Location' => $this->generateUrl('app_article_show',['id'=>$article->getID()], UrlGeneratorInterface::ABSOLUTE_URL)
			]
		);
	}

    /**
     * @Get(
     *     path = "/articles",
     *     name="app_article_list"
     * )
     * @QueryParam(
     *     name="keyword",
     *     requirements="[a-zA-Z0-9]",
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="15",
     *     description="Max number of movies per page."
     * )
     * @QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="The pagination offset"
     * )
     * @View
     */
    public function listAction(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->getDoctrine()->getRepository('AppBundle:Article')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );
        dump($pager);
        return new Articles($pager);
    }
}
