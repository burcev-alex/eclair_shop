<?

namespace App\Integration;

use Bitrix\Main;
use Bitrix\Main\Application;

class Event
{
	public static function onPageStart()
	{
		self::setupEventHandlers();
	}

	/**
	 * Добавляет обработчики событий
	 *
	 * @return void
	 */
	protected static function setupEventHandlers()
	{
		$eventManager = Main\EventManager::getInstance();

		// sale
	    $eventManager->addEventHandler('sale', 'OnSaleOrderSaved', ['\App\Integration\Handlers\Order', 'onSaleOrderSaved']);
	    $eventManager->addEventHandler('sale', 'OnSalePayOrder', ['\App\Integration\Handlers\Order', 'onSalePayOrder']);
	}
}
