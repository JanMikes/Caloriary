<?php declare (strict_types=1);

namespace JanMikes\Nutritionix;

use GuzzleHttp\Client;
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
		$response = $this->client->post('/v2/natural/nutrients', [
			'body' => [
				'query' => $text,
			],
		]);

		$body = (string) $response->getBody();
		$data = Json::decode($body);

		return array_map(function(\stdClass $food) {
			return new Food($food->food_name, $food->nf_calories);
		}, $data->foods);
	}
}
