<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Representation\Articles;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerInterface;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractFOSRestController
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ArticleRepository
     */
    private $articleRepository;


    /**
     * @var SerializerInterface
     */
    private $jmsSerializer;

    /**
     * ArticleController constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator, ArticleRepository $articleRepository, SerializerInterface $jmsSerializer)
    {
        $this->validator = $validator;
        $this->articleRepository = $articleRepository;
        $this->jmsSerializer = $jmsSerializer;
    }


    /**
     * @Rest\Get(path="", name="article_list")
     * @Rest\QueryParam(
     *     name = "keyword",
     *     requirements="[a-zA-Z0-9-_]",
     *     nullable = true,
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="20",
     *     description="Max number of articles per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="The pagination offset"
     * )
     *
     *
     */
    public function articleList(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->articleRepository->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        $result = new Articles($pager);
        return $this->view($pager->getCurrentPageResults(), Response::HTTP_OK);
    }

    /**
     * @Rest\Post(path = "", name="article_create")
     * @Rest\View(StatusCode = 201)
     *
     *
     */
    public function createArticle(Request $request)
    {
        $data = $request->getContent();
        $article = $this->jmsSerializer->deserialize($data, 'App\Entity\Article', 'json');


        $errors = $this->validator->validate($article);

        if (count($errors)) {

            return $this->view($errors, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        return $article;
    }

    /**
     * @Rest\Get(
     *     path = "/{id}",
     *     name="article_show",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(statusCode=200)
     */
    public function showArticle(Request $request, Article $article): View
    {
        return $this->view($article, Response::HTTP_OK);
    }

    /**
     * @Rest\Put(
     *     path = "/{id}",
     *     name="article_edit",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(statusCode=200)
     * @ParamConverter("newArticle", converter="fos_rest.request_body")
     */
    public function editArticle(Article $newArticle, Article $article): View
    {
        $article->setContent($newArticle->getContent());
        $article->setTitle($newArticle->getTitle());

        $errors = $this->validator->validate($article);

        if (count($errors) > 0) {
            return $this->view($errors, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->view($article, Response::HTTP_OK);
    }

    /**
     * @Rest\View(statusCode=204)
     * @Rest\Delete(
     *     path = "/{id}",
     *     name="article_delete",
     *     requirements={"id"="\d+"}
     * )
     */
    public function delete(Article $article)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();

        $message = "L'article a bien été supprimé !";
        return $this->view($message, Response::HTTP_OK);
    }
}
