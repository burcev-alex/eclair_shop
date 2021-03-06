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
 * Работа c шиной CRM
 * Class Crm
 */
class Crm extends Rest\Client\AbstractBase
{
	protected $url = "";
	protected $host = '';
	protected $api;
	private $token = "";
	private $userId;

	protected $format = "json";

	public function __construct($userId = 1)
	{
		// settings
		$this->url = Main\Config\Option::get('app.integration', 'crm_url', '');
		$this->host = Main\Config\Option::get('app.integration', 'crm_host', '');
		$this->token = Main\Config\Option::get('app.integration', 'crm_token', '');
		$this->format = Main\Config\Option::get('app.integration', 'crm_format', 'json');
		$this->userId = $userId;

		$option = array(
			'format' => $this->format
		);

		if(strlen($this->token) > 0){
			$option['headers']['X-ApiKey'] = $this->token;
			$option['headers']['X-ServiceAuthor'] = $userId;
			#$option['headers']['content_type'] = 'application/json;charset=utf-8';
		}

		$this->api = new Base\Rest\RestClient($option);
	}

	/**
	 * Отправка заказа
	 *
	 * @param $action
	 * @param $data
	 *
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function order($action, $data)
	{
		$id = 0;
		if($action != "add"){
			$id = $data['id'];
		}
		
		$response_json = array();
		$method = "order/".$id."/".$action."/";

		$requestString = json_encode($data); // JSON_UNESCAPED_UNICODE

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