if(!window.App)
    App = {};

App.extend = function(child, parent)
{
    var f = function() {};
    f.prototype = parent.prototype;

    child.prototype = new f();
    child.prototype.constructor = child;

    child.superclass = parent.prototype;
    child.prototype.superclass = parent.prototype;
    if(parent.prototype.constructor == Object.prototype.constructor)
    {
        parent.prototype.constructor = parent;
    }
};

if (!App)
	App = {};

if (!App.Shop)
	App.Shop = {};

App.Shop.CatalogSection = function (arParams, importantOffers) {
	this.product = arParams;
	this.important = importantOffers;
	this.name = {
		bigGalleryNode: 'galleryBigImages-',
		propsSkuNode: 'itemProps-'
	};
	this.galletysBigNode = {};
	this.itemsPropNode = {};
	this.itemsBntBasketNode = {};
	this.skuProps = arParams.SKU_PROPS;
	this.treeProps = {};
	this.collors = [];
	this.sizes = [];
	this.orientation = [];
	this.mas = {};
	this.offersFavoriteStatus = {};
	this.offersSelected = {};
	this.siteId = this.product.SITE_ID;
	this.init();
};

App.Shop.CatalogSection.prototype.init = function () {
	var obj = this.product.ITEMS;
	for (var key in obj) {
		if (obj.hasOwnProperty(key)) {
			this.galletysBigNode[key] = document.getElementById(this.name.bigGalleryNode + key);
			this.itemsPropNode[key] = document.getElementById(this.name.propsSkuNode + key);
			this.offersSelected[key] = obj[key].OFFER_ID_SELECTED;
		}
	}

	this.initProps();
	this.events();
	this.selectedImportantOffers();
};


App.Shop.CatalogSection.prototype.events = function () {
	$('a[data-action="add_basket"]').click(function(){
		let productId = $(this).data('product');
		let offerId = $(this).data('offer');
		
		app.onCustomEvent('onItemBuy', {});
		 let self = $(this);
		 BX.ajax({                
			 timeout:   30,
			 method:   'POST',
			 url: BX.message('POST_ACTION_URI') + '&ELEMENT_ID=' + productId,
			 processData: false,
			 data: {
				action: 'ADD2BASKET',
				id_top1: offerId,
				quantity: $('#item_quantity_'+productId).val()
			},
			onsuccess: function(reply){
				self.hide();
				self.parent().find('.section_key_cartlink').css('display', 'inline-block');
			},
			onfailure: function(){
				
			}
		});
		return BX.PreventDefault(event);
	});
};

App.Shop.CatalogSection.prototype.selectedImportantOffers = function () {

	for (var i = 0; i < this.important.length; i++) {
		let params = this.important[i];
		let offers = this.product.ITEMS[params.ID_PROD].OFFERS;
		for (let offerId in offers) {
			if(params.ID_VAZN_OFER == offerId){
				for (let propCode in this.skuProps) {
					var filterProp = offers[offerId].FILTER_PROPS[propCode];
					var list = this.itemsPropNode[params.ID_PROD].querySelectorAll('[data-entity="sku-line-block"] .product-item-scu-item[data-onevalue="'+filterProp.VALUE_ENUM_ID+'"]');
					for (var j = 0; j < list.length; j++) {
						this.fireEvent(list[j], 'click');
					}
				}
			}
		}
	}
};

/**
 * Инициализация свойства
 */
App.Shop.CatalogSection.prototype.initProps = function () {

	for (var key in this.itemsPropNode) {
		if (this.itemsPropNode.hasOwnProperty(key)) {
			var list = this.itemsPropNode[key].querySelectorAll('[data-entity="sku-line-block"] .product-item-scu-item');
			for (var i = 0; i < list.length; i++) {
				// событие на все значения характеристик
				BX.bind(list[i], 'click', BX.delegate(this.selectOfferProp, this));
			}
		}
	}
};

App.Shop.CatalogSection.prototype.fireEvent = function (node, eventName) {
	// Make sure we use the ownerDocument from the provided node to avoid cross-window problems
	var doc;
	if (node.ownerDocument) {
		doc = node.ownerDocument;
	} else if (node.nodeType == 9){
		// the node may be the document itself, nodeType 9 = DOCUMENT_NODE
		doc = node;
	} else {
		throw new Error("Invalid node passed to fireEvent: " + node.id);
	}

	if (node.dispatchEvent) {
		// Gecko-style approach (now the standard) takes more work
		var eventClass = "";

		// Different events have different event classes.
		// If this switch statement can't map an eventName to an eventClass,
		// the event firing is going to fail.
		switch (eventName) {
			case "click": // Dispatching of 'click' appears to not work correctly in Safari. Use 'mousedown' or 'mouseup' instead.
			case "mousedown":
			case "mouseup":
				eventClass = "MouseEvents";
				break;

			case "focus":
			case "change":
			case "blur":
			case "select":
				eventClass = "HTMLEvents";
				break;

			default:
				throw "fireEvent: Couldn't find an event class for event '" + eventName + "'.";
				break;
		}
		var event = doc.createEvent(eventClass);
		event.initEvent(eventName, true, true); // All events created as bubbling and cancelable.

		event.synthetic = true; // allow detection of synthetic events
		// The second parameter says go ahead with the default action
		node.dispatchEvent(event, true);
	} else  if (node.fireEvent) {
		// IE-old school style, you can drop this if you don't need to support IE8 and lower
		var event = doc.createEventObject();
		event.synthetic = true; // allow detection of synthetic events
		node.fireEvent("on" + eventName, event);
	}
};

/**
 * Выбор значений характеристик согласно дереву зависимостей
 */
App.Shop.CatalogSection.prototype.selectOfferProp = function () {
	var i = 0,
		value = '',
		strTreeValue = '',
		arTreeItem = [],
		rowItems = null,
		target = BX.proxy_context;

	if (target && target.hasAttribute('data-treevalue')) {
		if (BX.hasClass(target, 'active')) {
			//return;
		}

		// определить ID товара
		var parentContainer = target.closest('[data-entity="sku-block"]');
		var itemId = parseInt(parentContainer.getAttribute('data-entity-id'));

		// снять выделение с текущих, установить на выбранный блок
		var childElementList = target.closest('[data-entity="sku-line-block"]').querySelectorAll('.product-item-scu-item');
		for (var childIteration = 0; childIteration < childElementList.length; childIteration++) {
			BX.removeClass(childElementList[childIteration], 'active');
			BX.removeClass(childElementList[childIteration].querySelector('.sku-prop-value'), 'active');
		}

		let currentPropCode = target.closest('[data-entity="sku-line-block"] div.rightCol').getAttribute('data-code');

		BX.addClass(target.querySelector('.sku-prop-value'), 'active');
		BX.addClass(target, 'active');

		strTreeValue = target.getAttribute('data-treevalue');
		arTreeItem = strTreeValue.split('_');

		var isDisabled = false;
		for (var offerId in this.product.ITEMS[itemId].OFFERS) {
			var selectedOfferId = 0;
			var offerItem = this.product.ITEMS[itemId].OFFERS[offerId];

			var selectedPropCode = '';
			for (var propCode in this.skuProps) {
				if (typeof offerItem.FILTER_PROPS[propCode] != 'undefined') {
					var filterProp = offerItem.FILTER_PROPS[propCode];

					// нашли выбранный offer
					// определнить картинки для галереи и все параметры в дереве
					if (parseInt(arTreeItem[0]) === parseInt(filterProp.ID) && parseInt(arTreeItem[1]) === parseInt(filterProp.VALUE_ENUM_ID)) {

						if (!isDisabled) {
							this.offersSelected[itemId] = offerId;

							// деактивацтя всех характеристик
							for (var code in this.skuProps) {
								if (code != filterProp.CODE) {
									var productList = this.itemsPropNode[itemId].querySelectorAll('[data-entity="sku-line-block"] div.rightCol[data-code="' + code + '"] .product-item-scu-item');
									for (var k = 0; k < productList.length; k++) {
										BX.addClass(productList[k], 'disabled');
									}
								}
							}

							isDisabled = true;
						}

						// показать или скрыть элементы других характеристик относительно дерева зависимостей свойств
						this.selectedPropOffer(itemId, offerItem.FILTER_PROPS);
					}
				}
			}
		}

		this.updateNode(itemId);

		this.cleanDuplicateClass(itemId, currentPropCode);

		this.updateNode(itemId);

		// выбрать значения которые еще не выбраны
		// прогонка всех характеристик
		this.activeElementProp(itemId, currentPropCode);

		// установка значения выбранного ТП
		this.setData(itemId);
	}
};

App.Shop.CatalogSection.prototype.updateNode = function (itemId) {
	this.itemsPropNode[itemId] = document.getElementById(this.name.propsSkuNode + itemId);
};

/**
 * показать или скрыть элементы других характеристик относительно дерева зависимостей свойств
 *
 * @param itemId
 * @param offerItem
 */
App.Shop.CatalogSection.prototype.selectedPropOffer = function (itemId, offerItem) {
	for (var propCode in offerItem) {
		if (typeof offerItem[propCode] != 'undefined') {
			var filterProp = offerItem[propCode];
			if(filterProp) {
				if (typeof filterProp.VALUE_ENUM_ID != 'undefined') {
					var item = this.itemsPropNode[itemId].querySelectorAll('[data-entity="sku-line-block"] .product-item-scu-item[data-treevalue="' + filterProp.ID + '_' + filterProp.VALUE_ENUM_ID + '"]');
					for (var j = 0; j < item.length; j++) {
						BX.removeClass(item[j], 'disabled');
					}
				}
			}
		}
	}
};

/**
 * Активация первых элементов характеристики
 *
 * @param itemId
 * @param currentPropCode
 */
App.Shop.CatalogSection.prototype.activeElementProp = function (itemId, currentPropCode) {

	let property = this.itemsPropNode[itemId].querySelectorAll('[data-entity="sku-line-block"] div.rightCol');
	for (let j = 0; j < property.length; j++) {
		let propCode = property[j].getAttribute('data-code');

		if(propCode != currentPropCode){
			let child = property[j].querySelectorAll('.product-item-scu-item');
			let isActive = false;
			for (var n = 0; n < child.length; n++) {
				if (child[n].classList.contains('active')) {
					isActive = true;
				}
			}

			if(!isActive) {
				for (var i = 0; i < child.length; i++) {
					if (!child[i].classList.contains('disabled')) {
						BX.addClass(child[i].querySelector('.sku-prop-value'), 'active');
						BX.addClass(child[i], 'active');
						break;
					}
				}
			}
		}
	}
};

App.Shop.CatalogSection.prototype.cleanDuplicateClass = function (itemId, currentPropCode) {

	let property = this.itemsPropNode[itemId].querySelectorAll('[data-entity="sku-line-block"] div.rightCol');
	for (let j = 0; j < property.length; j++) {
		let propCode = property[j].getAttribute('data-code');

		if(propCode != currentPropCode){
			let child = property[j].querySelectorAll('.product-item-scu-item.disabled');
			for (var i = 0; i < child.length; i++) {
				if (child[i].classList.contains('active')) {
					BX.removeClass(child[i].querySelector('.sku-prop-value'), 'active');
					BX.removeClass(child[i], 'active');
				}
			}
		}
	}
};

/**
 * Разложить значения торгового предложения по соответствующим полям карточки
 *
 * @param itemID
 */
App.Shop.CatalogSection.prototype.setData = function (itemID) {
	var params = this.getSelectedParams(itemID);

	for (let offerId in this.product.ITEMS[itemID].OFFERS) {
		let filter = this.product.ITEMS[itemID].OFFERS[offerId].FILTER_PROPS;

		let iteration = 0;
		let iParam = 0;
		for (let propertyCode in params) {
			if (parseInt(filter[propertyCode].VALUE_ENUM_ID) === parseInt(params[propertyCode])) {
				iteration++;
			}
			iParam++;
		}

		if (iteration === iParam) {
			if(filter.PRICE) {
				if(filter.PRICE.VALUE) {
				this.setPrice(itemID, filter.PRICE.VALUE);
				this.setParamsToButton(itemID, offerId);
				}
			}
		}
	}
	
};

/**
 * Параметры выделенных свойств элемента
 *
 * @param itemID
 */
App.Shop.CatalogSection.prototype.getSelectedParams = function (itemID) {
	// все выбранные значения
	var params = {};
	var property = this.itemsPropNode[itemID].querySelectorAll('[data-entity="sku-line-block"] div.rightCol');
	for (var j = 0; j < property.length; j++) {
		var propCode = property[j].getAttribute('data-code');

		var child = property[j].querySelectorAll('.product-item-scu-item .sku-prop-value.active');
		for (var i = 0; i < child.length; i++) {
			params[propCode] = child[i].closest('.product-item-scu-item').getAttribute('data-onevalue');
		}
	}

	return params;
};

/**
 * Установка цены
 *
 * @param itemID
 * @param val
 */
App.Shop.CatalogSection.prototype.setPrice = function (itemID, val) {
	var price_list = document.querySelector('#itemPrice-' + itemID);
	if (price_list) {
		if(parseInt(val) > 0){
			price_list.innerHTML = val.toFixed(2)+' руб.';
		}
	}
};

App.Shop.CatalogSection.prototype.setParamsToButton = function (itemID, val) {
	var elButton = document.querySelector('#itemButtonBasket-' + itemID);
	if (elButton) {
		elButton.setAttribute('data-offer', val);
	}
};