<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Application\Pagination;

use BrandEmbassy\Slim\Request\RequestInterface;
use Caloriary\Application\Pagination\TotalItemsCounter;
use Nette\Utils\Paginator;

final class PaginatorFromRequestFactory
{
	/**
	 * @var int
	 */
	private $maxLimit;

	/**
	 * @var int
	 */
	private $defaultItemsPerPage;


	public function __construct(int $maxLimit, int $defaultItemsPerPage)
	{
		$this->maxLimit = $maxLimit;
		$this->defaultItemsPerPage = $defaultItemsPerPage;
	}


	/**
	 * @throws \InvalidArgumentException
	 */
	public function create(RequestInterface $request, TotalItemsCounter $itemsCounter): Paginator
	{
		$page = $request->getQueryParam('page', 1);
		$this->assertPageIsNumeric($page);

		$itemsPerPage = $request->getQueryParam('limit', $this->defaultItemsPerPage);
		$this->assertItemsPerPageIsNumeric($itemsPerPage);

		$paginator = new Paginator();

		$paginator->setPage($page);
		$paginator->setItemsPerPage(min($itemsPerPage, $this->maxLimit));
		$paginator->setItemCount($itemsCounter->__invoke());

		return $paginator;
	}


	/**
	 * @param int|string $page
	 *
	 * @throws \InvalidArgumentException
	 */
	private function assertPageIsNumeric($page): void
	{
		if (!is_numeric($page)) {
			throw new \InvalidArgumentException(sprintf(
				'Page must be numeric, \'%s\' given',
				$page
			));
		}
	}


	/**
	 * @param int|string $itemsPerPage
	 *
	 * @throws \InvalidArgumentException
	 */
	private function assertItemsPerPageIsNumeric($itemsPerPage): void
	{
		if (!is_numeric($itemsPerPage)) {
			throw new \InvalidArgumentException(sprintf(
				'Items per page must be numeric, \'%s\' given',
				$itemsPerPage
			));
		}
	}
}
