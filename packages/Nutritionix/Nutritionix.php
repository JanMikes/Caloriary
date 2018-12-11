<?php declare (strict_types=1);

namespace JanMikes\Nutritionix;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Nette\Utils\Json;

final class Nutritionix
{
	/**
	 * @var Client
	 */
	private $client;


	public function __construct(string $applicationId, string $apiKey)
	{
		$this->client = new Client([
			'base_uri' => 'https://trackapi.nutritionix.com/',
			'headers' => [
				'x-app-id' => $applicationId,
				'x-app-key' => $apiKey,
				'x-remote-user-id' => 0,
				'content-type' => 'application/json',
				'accept' => 'application/json',
			],
		]);
	}


	/**
	 * @return Food[]
	 */
	public function searchForFoods(string $text): array
	{
		try {
			$response = $this->client->post('/v2/natural/nutrients', [
				'body' => Json::encode([
					'query' => $text,
				]),
			]);

			$body = (string) $response->getBody();
			$data = Json::decode($body);

			return array_map(function(\stdClass $food) {
				return new Food($food->food_name, (int) $food->nf_calories);
			}, $data->foods);
		} catch (ClientException $e) {
			$response = $e->getResponse();

			if ($response->getStatusCode() === 404) {
				return [];
			}

			throw $e;
		}
	}
}
