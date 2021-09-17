<?php

namespace App\Controller;

use App\Domain\ArticleDAO;
use App\Domain\ArticleDO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
	 * @Route("/article/{slug}", name="get_article", methods={"GET"})
	 */
	public function getArticle(string $slug): Response
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
	 * @Route("/delete/{slug}", name="delete_article", methods={"GET"})
	 *
	 * @return Response|RedirectResponse
	 */
	public function deleteArticle(string $slug)
	{
		$articleDO = $this->articleDAO->find($slug);

		if (empty($articleDO)) {
			return $this->render('404.html.twig', []);
		}

		try {
			$this->articleDAO->delete($articleDO);
		} catch (BadRequestException $exception) {
			if (empty($articleDO)) {
				return $this->render('404.html.twig', []);
			}
		}

		return new RedirectResponse('../');
	}

	/**
	 * @Route("/creer", name="init_create_article", methods={"GET"})
	 */
	public function initCreate(): Response
	{
		return $this->render('article/create.html.twig', [
			'title' => '',
			'leading' => '',
			'body' => '',
			'createdBy' => '',
			'error' => '',
		]);
	}

	/**
	 * @Route("/creer", name="submit_create_article", methods={"POST"})
	 *
	 * @return Response|RedirectResponse
	 */
	public function submitCreate(Request $request)
	{
		$articleDO = new ArticleDO();
		$articleDO->setTitle($request->request->get('title'));
		$articleDO->setLeading($request->request->get('leading'));
		$articleDO->setBody($request->request->get('body'));
		$articleDO->setCreatedBy($request->request->get('createdBy'));

		try {
			$errors = [];
			if (empty($articleDO->getTitle())) {
				$errors[] = 'Le titre ne peut pas être vide.';
			}
			if (empty($articleDO->getCreatedBy())) {
				$errors[] = 'L\'auteur ne peut pas être vide.';
			}
			if (!empty($errors)) {
				throw new BadRequestException(implode(' ', $errors));
			}

			$articleDO = $this->articleDAO->insert($articleDO);
		} catch (BadRequestException $exception) {
			return $this->render('article/create.html.twig', [
				'title' => $articleDO->getTitle(),
				'leading' => $articleDO->getLeading(),
				'body' => $articleDO->getBody(),
				'createdBy' => $articleDO->getCreatedBy(),
				'error' => $exception->getMessage(),
			]);
		}

		return new RedirectResponse('article/' . $articleDO->getSlug());
	}
}
