<?

namespace App\Integration\Rest\Server;

use Bitrix\Main\Config;
use Bitrix\Main\Context;
use Bitrix\Main\DB;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use App\Base\Rest;
use App\Integration as Union;

class Main extends Rest\Api
{
	protected $apiKey;
	protected $debug = false;

	/**
	 * @var место хранения данных
	 */
	private $repository;

	public function __construct($request, $origin)
	{
		parent::__construct($request);

		if (Config\Option::get('app.integration', 'debug', 'N') == "Y") {
			$this->debug = true;
		}

		$this->apiKey = Config\Option::get('app.integration', 'server_rest_api');

		if (!array_key_exists('apiKey', $this->request)) {
			throw new \Exception('No API Key provided');
		} elseif ($this->apiKey != $this->request['apiKey']) {
			throw new \Exception('Invalid API Key');
		}

		$this->init();
	}

	/**
	 * Инициализация соединения
	 */
	private function init()
    {
		global $USER;

		// авторизуемся под админов
		$USER->Authorize(1);
	}

	/**
	 * Тестовый запрос
	 *
	 * @return array
	 */
	protected function test()
	{
		if ($this->method == 'GET') {
			return array(
				'status' => 'success',
				'data' => ["message" => "Hello world!"]
			);
		} else {
			return array(
				'status' => 'error',
				'data' => ["message" => "Only accepts GET requests"]
			);
		}
	}

	/**
	 * Добавление товара
	 *
	 * @return array
	 */
	protected function product()
	{
		if ($this->method == 'POST') {

			$resource = $this->request;
			$resource['file'] = $this->file;
			
			$guid = randString(12);

			$arData['status'] = "success";
			$arData['data'] = [
				'guid' => $guid
			];

			return $arData;
		} else {
			return array(
				'status' => 'error',
				'data' => ["message" => "Only accepts POST requests"]
			);
		}
	}

	/**
	 * Добавление раздела
	 *
	 * @return array
	 */
	protected function section()
	{
		if ($this->method == 'POST') {

			$resource = $this->request;
			$resource['file'] = $this->file;
			
			$guid = randString(12);

			$arData['status'] = "success";
			$arData['data'] = [
				'guid' => $guid
			];

			return $arData;
		} else {
			return array(
				'status' => 'error',
				'data' => ["message" => "Only accepts POST requests"]
			);
		}
	}

	/**
	 * Добавление предложения
	 *
	 * @return array
	 */
	protected function offers()
	{
		if ($this->method == 'POST') {

			$resource = $this->request;
			$resource['file'] = $this->file;
			
			$guid = randString(12);

			$arData['status'] = "success";
			$arData['data'] = [
				'guid' => $guid
			];

			return $arData;
		} else {
			return array(
				'status' => 'error',
				'data' => ["message" => "Only accepts POST requests"]
			);
		}
	}
}
