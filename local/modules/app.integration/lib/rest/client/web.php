<?

namespace App\Integration\Rest\Client;

use App\Base;
use Bitrix\Main;
use Bitrix\Main\Context;
use Bitrix\Main\DB;
use Bitrix\Iblock as Iblock;
use Bitrix\Main\Localization\Loc;
use App\Integration\Rest;

Loc::loadMessages(__FILE__);

/**
 * Работа c шиной 1C
 * Class Web
 */
class Web extends Rest\Client\AbstractBase
{
	protected $url = "";
	protected $host = '';
	protected $api;
	private $token = "";
	private $userId;
	private $login;
	private $pass;

	protected $format = "json";

	public function __construct($userId = 1)
	{
		// settings
		$this->url = Main\Config\Option::get('app.integration', 'website_url', '');
		$this->host = Main\Config\Option::get('app.integration', 'website_host', '');
		$this->token = Main\Config\Option::get('app.integration', 'website_token', '');
		$this->format = Main\Config\Option::get('app.integration', 'website_format', 'json');
		$this->login = Main\Config\Option::get('app.integration', 'website_login', 'admin');
		$this->pass = Main\Config\Option::get('app.integration', 'website_pass', '');
		$this->userId = $userId;

		$option = array(
			'format' => $this->format
		);
		if($this->login){
			$option['username'] = $this->login;
			$option['password'] = $this->pass;
		}

		if(strlen($this->token) > 0){
			$option['headers']['X-Service-Token'] = $this->token;
			$option['headers']['XServiceAuthor'] = $userId;
			#$option['headers']['content_type'] = 'application/json;charset=utf-8';
		}

		$this->api = new Base\Rest\RestClient($option);
	}

	/**
	 * Проверочный метод
	 *
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function test()
	{
		$method = "test/";

		try {
			$result = $this->api->get($this->url . $method, []);
			$response_json = $result->decode_response();

			$this->getWarningMessage($response_json, $result, $this->url . $method, []);

		} catch (\Exception $e) {
			$response_json = $this->getErrorMessage($this->url . $method, []);
			$response_json['errors'] = $e->getMessage();
		}

		return $response_json;
	}

	/**
	 * Отправка запроса об успешной оплате
	 *
	 * @param $data
	 *
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function addPayment($data)
	{
		$response_json = array();
		$method = "payment/";

		$requestString = json_encode($data); // JSON_UNESCAPED_UNICODE
		#p(json_encode($data, JSON_UNESCAPED_UNICODE));

		try {
			$result = $this->api->post($this->url . $method, $requestString);
			$response_json = $result->decode_response();

			$this->getWarningMessage($response_json, $result, $this->url . $method, $data);

		} catch (\Exception $e) {
			$response_json = $this->getErrorMessage($this->url . $method, $data);
			$response_json['errors'] = $e->getMessage();
		}

		return $response_json;
	}
}

?>