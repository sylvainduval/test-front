<?php

namespace App\Controller;

use App\Domain\ArticleDAO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
	private $articleDAO;

	public function __construct(ArticleDAO $articleDAO)
	{
		$this->articleDAO = $articleDAO;
	}

	/**
     * @Route("/", name="articles", methods={"GET"})
     */
    public function index(): Response
    {
    	$articleDOs = $this->articleDAO->findAll(1);

        return $this->render('article/index.html.twig', [
            'articles' => $articleDOs,
        ]);
    }

	/**
	 * @Route("/article/{slug}", name="article", methods={"GET"})
	 */
	public function article(string $slug): Response
	{
		$articleDO = $this->articleDAO->find($slug);

		if (empty($articleDO)) {
			return $this->render('404.html.twig', []);
		}

		return $this->render('article/article.html.twig', [
			'article' => $articleDO,
		]);
	}

	/**
	 * @Route("/create", name="create", methods={"GET"})
	 */
	public function initCreate(): Response
	{
		return $this->render('article/create.html.twig', []);
	}

	/**
	 * @Route("/create", name="create", methods={"POST"})
	 */
	public function submitCreate(Request $request): Response
	{
		dd($request->request->all());
	}
}
