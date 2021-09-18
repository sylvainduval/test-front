<?php
namespace App\Controller;

use App\Domain\ArticleDAO;
use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ArticleController extends AbstractController
{
	private $articleDAO;

	public function __construct(ArticleDAO $articleDAO)
	{
		$this->articleDAO = $articleDAO;
	}

	/**
	 * @Route("/{page}", requirements={"page"="\d+"}, defaults={"page"="1"}, name="articles", methods={"GET"})
	 */
	public function index(int $page): Response
	{
		$articles = $this->articleDAO->findAll($page ?: 1);

		return $this->render('article/index.html.twig', [
			'articles' => $articles,
		]);
	}

	/**
	 * @Route("/article/{slug}", name="get_article", methods={"GET"})
	 */
	public function getArticle(string $slug): Response
	{
		$article = $this->articleDAO->find($slug);

		if (empty($article)) {
			throw new NotFoundHttpException('Article introuvable');
		}

		return $this->render('article/article.html.twig', [
			'article' => $article,
		]);
	}

	/**
	 * @Route("/delete/{slug}", name="delete_article", methods={"GET"})
	 *
	 * @return Response|RedirectResponse
	 */
	public function deleteArticle(string $slug)
	{
		$article = $this->articleDAO->find($slug);

		if (empty($article)) {
			throw new NotFoundHttpException('Article introuvable');
		}

		try {
			$this->articleDAO->delete($article);
		} catch (BadRequestException $exception) {
			if (empty($article)) {
				throw new NotFoundHttpException('Article introuvable');
			}
		}

		return new RedirectResponse('../');
	}

	/**
	 * @Route("/creer", name="init_create_article", methods={"GET", "POST"})
	 */
	public function create(Request $request, SerializerInterface $serializer): Response
	{
		$form = $this->createForm(ArticleType::class);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$article = $serializer->deserialize(json_encode($form->getData()), Article::class, 'json');
			try {
				$article = $this->articleDAO->insert($article);
			} catch (BadRequestException $exception) {
				throw new BadRequestHttpException($exception->getMessage());
			}

			return new RedirectResponse('article/' . $article->getSlug());
		}

		return $this->render('article/create.html.twig', [
			'form' => $form->createView(),
		]);
	}
}
