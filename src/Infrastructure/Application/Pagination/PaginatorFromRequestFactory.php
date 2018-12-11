<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Application\Pagination;

use BrandEmbassy\Slim\Request\RequestInterface;
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
	public function create(RequestInterface $request, int $itemsCount): Paginator
	{
		$page = $request->getQueryParam('page', 1);

		$this->assertPageIsNumeric($page);

		// @TODO throw exception when limit is out of scope || is not numeric
		$itemsPerPage = $request->getQueryParam('limit', $this->defaultItemsPerPage);

		$paginator = new Paginator();

		$paginator->setPage($page);
		$paginator->setItemsPerPage($itemsPerPage);
		$paginator->setItemCount($itemsCount);

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
			throw new \InvalidArgumentException('Page must be numeric');
		}
	}
}
