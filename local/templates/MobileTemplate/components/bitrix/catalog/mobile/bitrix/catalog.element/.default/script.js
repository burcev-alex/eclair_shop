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

App.Shop.CatalogElement = function (arParams) {
	this.preinit();

	this.container = document.getElementById(arParams.CONTAINER);
	this.product = arParams;
	this.iteration = 0;
	this.documentWidth = document.documentElement.clientWidth;
	this.name = {
		propsSkuNode: 'elementProps-'+arParams.PRODUCT_ID,
	};
	this.productPropNode = [];
	this.skuProps = arParams.OFFERS_PROPS;
	this.isBindOffer = arParams.IS_BIND_OFFER;
	this.treeProps = {};
	this.offersFavoriteStatus = {};
	this.offersSelected = {};
	this.siteId = this.product.SITE_ID;
	this.useHash = true;
	this.price = 0;
	this.params = {
		hash: '',
		hashOfferId: 0,
		hashColor: '',
		hashSize: '',
		hashOrientation: ''
	};

	var self = this;

	if(this.documentWidth<860){
		setTimeout(function(){
			self.init();
		}, 100);
	}
	else{
		this.init();
	}


};

App.Shop.CatalogElement.prototype.preinit = function () {
	
};

App.Shop.CatalogElement.prototype.init = function () {
	this.productPropNode = document.getElementById(this.name.propsSkuNode);
	
	if (this.useHash === true) {
		this.getUrlHash();
		this.getCurrentOffer();
	}
	else{
		this.offersSelected[this.product.PRODUCT_ID] = this.product.OFFER_ID_SELECTED;
	}

	this.initProps();

	if (this.useHash === true) {
		this.selectedImportantOffers();
	}

	this.initBasket();

	this.events();
};

App.Shop.CatalogElement.prototype.events = function () {
	let container = this.product.CONTAINER;
	let self = this;
	
	$('#'+container).find('[data-action="add_basket"]').click(function(){
		let el = $(this);
		
		$('#'+container).addClass('add2cart');
		el.text('В корзине');

		app.onCustomEvent('onItemBuy', {});

		BX.ajax({
			timeout:   30,
			method:   'GET',
			url:       el.attr('href'),
			processData: false,
			onsuccess: function(reply){
				setTimeout(function(){
					$('#'+container).removeClass('add2cart');
					el.text('Купить');
				}, 2000);
			},
			onfailure: function(error){
				console.log(error);
			}
		});

		
		return false;
	});
};

App.Shop.CatalogElement.prototype.initBasket = function () {
	let self = this;
	let container = this.container.querySelector('[data-entity="sku-block"] .amount');
	
};

App.Shop.CatalogElement.prototype.selectedImportantOffers = function () {

	let offers = this.product.OFFERS;
	for (let offerId in offers) {
		if(parseInt(this.offersSelected[this.product.PRODUCT_ID]) == parseInt(offerId)){
			for (let propCode in this.skuProps) {
				var filterProp = offers[offerId].FILTER_PROPS[propCode];
				
				var item = this.productPropNode.querySelectorAll('[data-entity="sku-block"] div[data-code="'+propCode+'"] .right .sku-value[data-onevalue="'+filterProp.VALUE_ENUM_ID+'"]');
				
				for (var j = 0; j < item.length; j++) {
					this.fireEvent(item[j], 'click');
				}
			}
		}
	}
};

/**
 * Инициализация свойства
 */
App.Shop.CatalogElement.prototype.initProps = function () {
	let list = this.productPropNode.querySelectorAll('.product-scu-container .right .sku-value');
	for (let i = 0; i < list.length; i++) {
		// событие на все значения характеристик
		if(this.isBindOffer) {
			BX.bind(list[i], 'click', BX.delegate(this.selectOfferBindProp, this));
		}
		else{
			BX.bind(list[i], 'click', BX.delegate(this.selectOfferProp, this));
		}
	}
};

App.Shop.CatalogElement.prototype.fireEvent = function (node, eventName) {
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
App.Shop.CatalogElement.prototype.selectOfferProp = function () {
	var i = 0,
		value = '',
		strTreeValue = '',
		arTreeItem = [],
		rowItems = null,
		target = BX.proxy_context;

	let selected = target.closest('[data-entity="sku-line-block"]').querySelectorAll('.right .sku-value');
	for (let j = 0; j < selected.length; j++) {
		BX.removeClass(selected[j], 'active');
	}

	if (target && (parseInt(target.getAttribute('data-onevalue')) > 0)) {
		if (BX.hasClass(target, 'active')) {
			return;
		}

		BX.addClass(target, 'active');

		// определить ID товара
		let parentContainer = target.closest('[data-entity="sku-block"]');

		strTreeValue = target.getAttribute('data-treevalue');
		arTreeItem = strTreeValue.split('_');

		for (var offerId in this.product.OFFERS) {
			var selectedOfferId = 0;
			var offerItem = this.product.OFFERS[offerId];

			var selectedPropCode = '';
			for (var propCode in this.skuProps) {
				if (typeof offerItem.FILTER_PROPS[propCode] != 'undefined') {
					var filterProp = offerItem.FILTER_PROPS[propCode];

					// нашли выбранный offer
					// определить все параметры в дереве
					if (parseInt(arTreeItem[0]) === parseInt(filterProp.ID) && parseInt(arTreeItem[1]) === parseInt(filterProp.VALUE_ENUM_ID)) {
						// показать или скрыть элементы других характеристик относительно дерева зависимостей свойств
						this.selectedPropOffer(offerItem.FILTER_PROPS);
					}
				}
			}
		}

		this.updateNode();

		// установка значения выбранного ТП
		this.setData();
	}
	else{
		BX.addClass(target, 'active');

		this.cleanPrice();
	}
};

/**
 * Выбор значений характеристик согласно дереву зависимостей c жесткой зависимостью
 */
App.Shop.CatalogElement.prototype.selectOfferBindProp = function () {
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
		var childElementList = target.closest('[data-entity="sku-line-block"]').querySelectorAll('.right .sku-value');
		for (var childIteration = 0; childIteration < childElementList.length; childIteration++) {
			BX.removeClass(childElementList[childIteration], 'active');

			if(childElementList[childIteration].querySelector('input')){
				childElementList[childIteration].querySelector('input').removeAttribute('checked');
			}
		}

		let currentPropCode = target.closest('div[data-entity="sku-line-block"]').getAttribute('data-code');

		BX.addClass(target.querySelector('.right .sku-value'), 'active');
		BX.addClass(target, 'active');

		if(target.querySelector('input')){
			target.querySelector('input').setAttribute('checked', true);
		}

		strTreeValue = target.getAttribute('data-treevalue');
		arTreeItem = strTreeValue.split('_');
		
		var isDisabled = false;
		for (var offerId in this.product.OFFERS) {
			var selectedOfferId = 0;
			var offerItem = this.product.OFFERS[offerId];

			var selectedPropCode = '';
			for (var propCode in this.skuProps) {
				if (typeof offerItem.FILTER_PROPS[propCode] != 'undefined') {
					var filterProp = offerItem.FILTER_PROPS[propCode];

					// нашли выбранный offer
					// определить все параметры в дереве
					if (parseInt(arTreeItem[0]) === parseInt(filterProp.ID) && parseInt(arTreeItem[1]) === parseInt(filterProp.VALUE_ENUM_ID)) {

						if (!isDisabled) {
							this.offersSelected[itemId] = offerId;

							// деактивацтя всех характеристик
							for (var code in this.skuProps) {
								if(code != "RAZMER"){
									if (code != filterProp.CODE) {
										var productList = this.productPropNode.querySelectorAll('div.line[data-code="' + code + '"] .right .sku-value');
										for (var k = 0; k < productList.length; k++) {
											BX.addClass(productList[k], 'disabled');

											if(productList[k].querySelector('input')){
												productList[k].querySelector('input').setAttribute('checked', false);
											}
										}
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

		this.updateNode();

		this.cleanDuplicateClass(itemId, currentPropCode);

		this.updateNode(itemId);

		// выбрать значения которые еще не выбраны
		// прогонка всех характеристик
		this.activeElementProp(itemId, currentPropCode);

		// установка значения выбранного ТП
		this.setData(itemId);
	}
	else{
		BX.addClass(target, 'active');

		this.cleanPrice();
	}
};

App.Shop.CatalogElement.prototype.updateNode = function () {
	this.productPropNode = document.getElementById(this.name.propsSkuNode);
};

/**
 * Активация первых элементов характеристики
 *
 * @param itemId
 * @param currentPropCode
 */
App.Shop.CatalogElement.prototype.activeElementProp = function (itemId, currentPropCode) {

	let property = this.productPropNode.querySelectorAll('[data-entity="sku-block"] div[data-entity="sku-line-block"]');
	for (let j = 0; j < property.length; j++) {
		let propCode = property[j].getAttribute('data-code');

		if(propCode != currentPropCode){
			let child = property[j].querySelectorAll('.right .sku-value');
			let isActive = false;
			for (var n = 0; n < child.length; n++) {
				if (child[n].classList.contains('active')) {
					isActive = true;
				}
			}

			if(!isActive) {
				for (var i = 0; i < child.length; i++) {
					if (!child[i].classList.contains('disabled')) {
						if(this.documentWidth<860) {
							if (propCode != 'OGRANICHITELI') {
								if (property[j].querySelector('.choice .sku-value')) {
									property[j].querySelector('.choice .sku-value').remove();
								}
								property[j].querySelector('.choice').appendChild(child[i]);
							} else {
								if (property[j].querySelector('.choice')) {
									property[j].querySelector('.choice').innerHTML = '';
								}
								property[j].querySelector('.choice').innerHTML = child[i].textContent;
							}
						}

						BX.addClass(child[i].querySelector('.right .sku-value'), 'active');
						BX.addClass(child[i], 'active');

						if(child[i].querySelector('input')){
							child[i].querySelector('input').setAttribute('checked', true);
						}
						break;
					}
				}
			}
		}
	}
};

App.Shop.CatalogElement.prototype.cleanDuplicateClass = function (itemId, currentPropCode) {

	let property = this.productPropNode.querySelectorAll('[data-entity="sku-line-block"]');
	for (let j = 0; j < property.length; j++) {
		let propCode = property[j].getAttribute('data-code');

		if(propCode != currentPropCode){
			let child = property[j].querySelectorAll('.right .sku-value.disabled');
			for (var i = 0; i < child.length; i++) {
				if (child[i].classList.contains('active')) {
					BX.removeClass(child[i].querySelector('.right .sku-value'), 'active');
					BX.removeClass(child[i], 'active');

					if(child[i].querySelector('input')){
						child[i].querySelector('input').setAttribute('checked', true);
					}
				}
			}
		}
	}
};

/**
 * показать или скрыть элементы других характеристик относительно дерева зависимостей свойств
 *
 * @param itemId
 * @param offerItem
 */
App.Shop.CatalogElement.prototype.selectedPropOffer = function (itemId, offerItem) {
	for (var propCode in offerItem) {
		if (typeof offerItem[propCode] != 'undefined') {
			var filterProp = offerItem[propCode];
			if(filterProp) {
				if (typeof filterProp.VALUE_ENUM_ID != 'undefined') {
					var item = this.productPropNode.querySelectorAll('[data-entity="sku-block"] .right .sku-value[data-treevalue="' + filterProp.ID + '_' + filterProp.VALUE_ENUM_ID + '"]');
					for (var j = 0; j < item.length; j++) {
						BX.removeClass(item[j], 'disabled');
					}
				}
			}
		}
	}
};

App.Shop.CatalogElement.prototype.setButtonToBasket = function (offerID) {
	var self = this;
	var basketBtn = this.container.querySelector('[data-action="add_basket"]');
	basketBtn.setAttribute('data-product', offerID);
	
	basketBtn.setAttribute('href', '/eshop_app/catalog/?action=ADD2BASKET&id_top1='+offerID+'&SECTION_ID='+this.product.PRODUCT_SECT_ID+'&ELEMENT_ID='+this.product.PRODUCT_ID);
};

/**
 * Разложить значения торгового предложения по соответствующим полям карточки
 */
App.Shop.CatalogElement.prototype.setData = function () {
	var params = this.getSelectedParams();

	this.cleanPrice();

	for (let offerId in this.product.OFFERS) {
		let offer = this.product.OFFERS[offerId];
		let filter = this.product.OFFERS[offerId].FILTER_PROPS;

		let iteration = 0;
		let iParam = 0;
		for (let propertyCode in params) {
			if (parseInt(filter[propertyCode].VALUE_ENUM_ID) === parseInt(params[propertyCode])) {
				iteration++;
			}
			iParam++;
		}

		if (iteration === iParam) {

			this.setUrlHash(offerId);

			if(filter.PRICE) {
				this.setPrice(parseFloat(filter.PRICE.VALUE));
				this.setButtonToBasket(offerId);
			}
		}
	}
};

/**
 * Параметры выделенных свойств элемента
 */
App.Shop.CatalogElement.prototype.getSelectedParams = function () {
	// все выбранные значения
	var params = {};

	$('#'+this.name.propsSkuNode).find('[data-entity="sku-line-block"]').each(function(){
		let propCode = $(this).attr('data-code');

		$(this).find('.right .sku-value.active').each(function(){
			params[propCode] = $(this).closest('.sku-value').attr('data-onevalue');
		});
	});

	return params;
};
App.Shop.CatalogElement.prototype.getSelectedParamsNative = function () {
	// все выбранные значения
	var params = {};
	var property = this.productPropNode.querySelectorAll('[data-entity="sku-line-block"]');
	for (let j = 0; j < property.length; j++) {
		var propCode = property[j].getAttribute('data-code');

		var child = property[j].querySelectorAll('.right .sku-value.active');
		for (let i = 0; i < child.length; i++) {
			params[propCode] = child[i].closest('.sku-value').getAttribute('data-onevalue');
		}

		var blockInput = property[j].querySelectorAll('.right .sku-value input[type="radio"]');
		for (let k = 0; k < blockInput.length; k++) {
			if(blockInput[k].checked == true) {
				params[propCode] = blockInput[k].closest('.sku-value').getAttribute('data-onevalue');
			}
		}
	}

	return params;
};
/**
 * Установка цены
 *
 * @param val
 */
App.Shop.CatalogElement.prototype.setPrice = function (val) {
	var priceList = document.querySelectorAll('.productPrice');
	this.price = val;
	for (let j = 0; j < priceList.length; j++) {
		let price_list = priceList[j];
		if (price_list) {
			BX.removeClass(price_list.querySelector('.prefix-priceOffer'), 'd-none');
			BX.removeClass(price_list.querySelector('.sufix-priceOffer'), 'd-none');
			BX.removeClass(this.container.querySelector('.addtocart-button'), 'd-none');
			price_list.querySelector('.priceOffer').innerHTML = val.toFixed(2);

			BX.removeClass(this.container.querySelector('[data-action="add_basket"]'), 'd-none');

			BX.addClass(this.container.querySelector('[data-action="find_out_price"]'), 'd-none');
		}
	}

	var priceList = document.querySelectorAll('.productRollPrice');
	for (let j = 0; j < priceList.length; j++) {
		let price_list = priceList[j];
		if (price_list) {
			var valRoll =  val.toFixed(2) * this.product.UNIT_COEFFICIENT;
			BX.removeClass(price_list.querySelector('.prefix-priceOffer'), 'd-none');
			BX.removeClass(price_list.querySelector('.sufix-priceOffer'), 'd-none');
			price_list.querySelector('.priceOffer').innerHTML = valRoll.toFixed(2);
		}
	}
};

App.Shop.CatalogElement.prototype.cleanPrice = function () {
	var priceList = document.querySelectorAll('.productPrice');
	for (let j = 0; j < priceList.length; j++) {
		let price_list = priceList[j];
		BX.addClass(price_list.querySelector('.prefix-priceOffer'), 'd-none');
		BX.addClass(price_list.querySelector('.sufix-priceOffer'), 'd-none');
		BX.addClass(this.container.querySelector('.addtocart-button'), 'd-none');
		price_list.querySelector('.priceOffer').innerHTML = BX.message('TITLE_PRICE_GET_INFO');
	}

	BX.addClass(this.container.querySelector('[data-action="add_basket"]'), 'd-none');

	BX.removeClass(this.container.querySelector('[data-action="find_out_price"]'), 'd-none');
};

App.Shop.CatalogElement.prototype.getUrlHash = function() {
	var arHash = void 0,
		oneParam = void 0;
	var hash = window.location.hash.replace('#', '');

	if (hash) {
		this.hash = hash;

		arHash = hash.split('&');
		for (var key in arHash) {
			if (arHash.hasOwnProperty(key)) {
				oneParam = arHash[key].split('=');
				switch (oneParam[0]) {
					case 'ID':
						this.params.hashOfferId = parseInt(oneParam[1]);
						break;
					case 'TSVET':
						this.params.hashColor = oneParam[1];
						break;
					case 'RAZMER':
						this.params.hashSize = oneParam[1];
						break;
				}
			}
		}
	}
}

App.Shop.CatalogElement.prototype.setUrlHash = function(offerId) {
	var hash = '';

	hash += 'ID=' + offerId;
	window.location.hash = hash;
}

App.Shop.CatalogElement.prototype.getCurrentOffer = function() {
	if (parseInt(this.params.hashOfferId) > 0) {
		this.offersSelected[this.product.PRODUCT_ID] = this.params.hashOfferId;
	} else {
		this.offersSelected[this.product.PRODUCT_ID] = this.product.OFFER_ID_SELECTED;
	}
}