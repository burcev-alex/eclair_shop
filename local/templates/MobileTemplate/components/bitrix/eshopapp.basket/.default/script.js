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

App.Shop.Baskets = function (arParams) {
	this.product = arParams;
	this.name = {
		propsSkuNode: 'itemProps-'
	};
	this.galletysBigNode = {};
	this.itemsPropNode = {};
	this.skuProps = arParams.PROPS;
	this.treeProps = {};
	this.offersSelected = {};
	this.init();
};

App.Shop.Baskets.prototype.init = function () {
	var obj = this.product.ITEMS;
	for (var key in obj) {
		if (obj.hasOwnProperty(key)) {
			this.itemsPropNode[obj[key].ID] = document.getElementById(this.name.propsSkuNode + obj[key].ID);
			this.offersSelected[obj[key].ID] = obj[key].PRODUCT_ID;
		}
	}

	this.initProps();
	this.events();
};


App.Shop.Baskets.prototype.events = function () {
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

/**
 * Инициализация свойства
 */
App.Shop.Baskets.prototype.initProps = function () {
	
	for (var key in this.itemsPropNode) {
		
		if (this.itemsPropNode[key] && this.itemsPropNode.hasOwnProperty(key)) {
			var list = this.itemsPropNode[key].querySelectorAll('[data-entity="basket-item-sku-block"] .basket-item-scu-item');
			for (var i = 0; i < list.length; i++) {
				// событие на все значения характеристик
				BX.bind(list[i], 'click', BX.delegate(this.selectOfferProp, this));
			}
		}
	}
};

App.Shop.Baskets.prototype.fireEvent = function (node, eventName) {
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
App.Shop.Baskets.prototype.selectOfferProp = function () {
	var i = 0, target = BX.proxy_context;

	if (target && target.hasAttribute('data-value-id')) {
		if (BX.hasClass(target, 'active')) {
			return;
		}

		// определить ID товара
		var parentContainer = target.closest('[data-entity="sku-block"]');
		var itemId = parseInt(parentContainer.getAttribute('data-basket-id'));

		// снять выделение с текущих, установить на выбранный блок
		var childElementList = target.closest('[data-entity="basket-item-sku-block"]').querySelectorAll('.basket-item-scu-item');
		for (var childIteration = 0; childIteration < childElementList.length; childIteration++) {
			BX.removeClass(childElementList[childIteration], 'active');
		}
		
		BX.addClass(target, 'active');

		let propertyCode = target.getAttribute('data-property');
		let enumId = target.getAttribute('data-value-id');

		let selectedProp = [];

		var propList = parentContainer.querySelectorAll('.basket-item-scu-item.active');
		for (var propIteration = 0; propIteration < propList.length; propIteration++) {
			selectedProp.push({
				'code': propList[propIteration].getAttribute('data-property'),
				'value': propList[propIteration].getAttribute('data-value-id')
			});
		}
		
		
		var isDisabled = false;
		for (var offerId in this.skuProps[itemId]) {
			if (typeof this.skuProps[itemId] != 'undefined') {
				var filterProp = this.skuProps[itemId][offerId];
				
				for (var selected in selectedProp) {
					// нашли выбранный offer
					if(filterProp[selectedProp[selected].code] == selectedProp[selected].value){
						if (!isDisabled) {
							this.offersSelected[itemId] = offerId;

							// деактивацтя всех характеристик
							/*
							for (var tmpOfferId in this.skuProps[itemId]) {
								if (typeof this.skuProps[itemId] != 'undefined') {
									if (selectedProp[selected].code != propertyCode) {
										var productList = this.itemsPropNode[itemId].querySelectorAll('.basket-item-scu-item[data-property="' + selectedProp[selected].code + '"]');
										for (var k = 0; k < productList.length; k++) {
											BX.addClass(productList[k], 'disabled');
										}
									}
								}
							}
							*/

							isDisabled = true;
						}

						// показать или скрыть элементы других характеристик относительно дерева зависимостей свойств
						//this.selectedPropOffer(itemId, this.skuProps[itemId]);
					}
				}
			}
		}

		// выбрать значения которые еще не выбраны
		// прогонка всех характеристик
		//this.activeElementProp(itemId, propertyCode);

		// установка значения выбранного ТП
		this.setData(itemId);
	}
};

/**
 * показать или скрыть элементы других характеристик относительно дерева зависимостей свойств
 *
 * @param itemId
 * @param offerItem
 */
App.Shop.Baskets.prototype.selectedPropOffer = function (itemId, offerItem) {
	
};

/**
 * Активация первых элементов характеристики
 *
 * @param itemId
 * @param currentPropCode
 */
App.Shop.Baskets.prototype.activeElementProp = function (itemId, currentPropCode) {

	let property = this.itemsPropNode[itemId].querySelectorAll('[data-entity="basket-item-sku-block"]');
	for (let j = 0; j < property.length; j++) {
		let propCode = property[j].getAttribute('data-property');

		if(propCode != currentPropCode){
			let child = property[j].querySelectorAll('.basket-item-scu-item');
			let isActive = false;
			for (var n = 0; n < child.length; n++) {
				if (child[n].classList.contains('active')) {
					isActive = true;
				}
			}

			if(!isActive) {
				for (var i = 0; i < child.length; i++) {
					if (!child[i].classList.contains('disabled')) {
						BX.addClass(child[i], 'active');
						break;
					}
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
App.Shop.Baskets.prototype.setData = function (itemID) {
	var params = this.getSelectedParams(itemID);

	// поиск ТП по параметрам
	selectedOfferId = 0;

	for (var offerId in this.skuProps[itemID]) {
		if (typeof this.skuProps[itemID] != 'undefined') {
			var filterProp = this.skuProps[itemID][offerId];
			
			if(this.objDiff(params, filterProp)){
				selectedOfferId = parseInt(offerId);
			}
			
		}
	}

	if(selectedOfferId > 0){
		this.changeOffer(itemID, selectedOfferId, this.product.PRICE[itemID][selectedOfferId]);
	}
	
};

App.Shop.Baskets.prototype.objDiff = function(a, b) {
	let self = this;

	//if a and b aren't the same type, they can't be equal
    if (typeof a !== typeof b) {
        return false;
    }

    if (typeof a === 'object') {
        var keysA = Object.keys(a).sort(),
            keysB = Object.keys(b).sort();

        //if a and b are objects with different no of keys, unequal
        if (keysA.length !== keysB.length) {
            return false;
        }

        //if keys aren't all the same, unequal
        if (!keysA.every(function(k, i) { return k === keysB[i];})) {
            return false;
        }

        //recurse on the values for each key
        return keysA.every(function(key) {
            //if we made it here, they have identical keys
            return self.objDiff(a[key], b[key]);
        });

    //for primitives just use a straight up check
    } else {
        return a === b;
    }
}

/**
 * Параметры выделенных свойств элемента
 *
 * @param itemID
 */
App.Shop.Baskets.prototype.getSelectedParams = function (itemID) {
	// все выбранные значения
	var params = {};
	var property = this.itemsPropNode[itemID].querySelectorAll('[data-entity="basket-item-sku-block"]');
	for (var j = 0; j < property.length; j++) {
		var propCode = property[j].getAttribute('data-property');

		var child = property[j].querySelectorAll('.basket-item-scu-item.active');
		for (var i = 0; i < child.length; i++) {
			params[propCode] = child[i].closest('.basket-item-scu-item').getAttribute('data-value-id');
		}
	}

	return params;
};
App.Shop.Baskets.prototype.getSelectedParamsValue = function (itemID) {
	// все выбранные значения
	var params = {};
	var property = this.itemsPropNode[itemID].querySelectorAll('[data-entity="basket-item-sku-block"]');
	for (var j = 0; j < property.length; j++) {
		var propCode = property[j].getAttribute('data-property');

		var child = property[j].querySelectorAll('.basket-item-scu-item.active');
		for (var i = 0; i < child.length; i++) {
			params[propCode] = child[i].closest('.basket-item-scu-item').getAttribute('data-sku-name');
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
App.Shop.Baskets.prototype.changeOffer = function (basketItemId, offerId, val) {
	this.setPrice(basketItemId, val);

	let properties = this.getSelectedParamsValue(basketItemId);
	
	BX.ajax({
		timeout:   30,
		method:   'POST',
		url: '/local/templates/MobileTemplate/components/bitrix/eshopapp.basket/.default/ajax.php',
		data: {
			ACTION: 'change',
			ID: basketItemId,
			PRODUCT_ID: offerId,
			PRICE: val,
			PROPS: properties
		},
		processData: false,
		onsuccess: function(reply){
			var json = JSON.parse(reply);

			if(json.status == 'ok'){
				calcBasket();
			}
		}
	});
	
};

/**
 * Установка цены
 *
 * @param itemID
 * @param val
 */
App.Shop.Baskets.prototype.setPrice = function (itemID, val) {
	var price_list = document.querySelector('#itemPrice-' + itemID);
	
	if (price_list) {
		if(parseInt(val) > 0){
			price_list.innerHTML = parseFloat(val).toFixed(0)+' руб.';
		}
	}
};

// BX.ready(function(){
//
//
//     $('.quantity_input').change(function() {
//         calcBasket();
//     });
// });

function calcBasket () {
    var data_form = {}, form = BX('basket_form');
    for(var i = 0; i< form.elements.length; i++)
    {
        if (form[i].name != 'BasketOrder')
            data_form[form[i].name] = form[i].value;
    }
    ajaxInCart('/eshop_app/personal/cart/', data_form);
}