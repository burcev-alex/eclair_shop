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

		if (!array_key_exists('x-apikey', $this->headers)) {
            throw new \Exception('No API Key provided');
        } elseif ($this->apiKey != $this->headers['x-apikey']) {
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
            return [
                'status' => 'success',
                'data' => [
                    'message' => 'Hello world!',
                    'header' => $this->headers,
                ],
            ];
        } else {
            return [
                'status' => 'error',
                'data' => ['message' => 'Only accepts GET requests'],
            ];
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

			if(array_key_exists('request', $this->request)){
				$resource = $this->request['request'];

				$tmp = explode("/", $resource);

				$method = $tmp[0];
				$id = $tmp[1];
				$action = $tmp[2];
			}
			else{
				$method = $this->content['params']['method'];
				$id = $this->content['params']['id'];
				$action = $this->content['params']['action'];
			}
			
			$service = new Union\Services\Section();
			if($action == "add"){
				$guid = $service->save($this->content);
			}
			else if($action == "update"){
				$guid = $service->save($this->content);
			}
			else if($action == "delete"){
				$guid = $service->delete($this->content);
			}

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
	 * Добавление свойств
	 *
	 * @return array
	 */
	protected function property()
	{
		if ($this->method == 'POST') {

			if(array_key_exists('request', $this->request)){
				$resource = $this->request['request'];

				$tmp = explode("/", $resource);

				$method = $tmp[0];
				$id = $tmp[1];
				$action = $tmp[2];
			}
			else{
				$method = $this->content['params']['method'];
				$id = $this->content['params']['id'];
				$action = $this->content['params']['action'];
			}
			
			$service = new Union\Services\Property();
			if($action == "add"){
				$guid = $service->save($this->content);
			}
			else if($action == "update"){
				$guid = $service->save($this->content);
			}
			else if($action == "delete"){
				$guid = $service->delete($this->content);
			}

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
