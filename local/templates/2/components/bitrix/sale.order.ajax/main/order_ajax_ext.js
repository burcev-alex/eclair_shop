(function(){
    'use strict';

    var initParent = BX.Sale.OrderAjaxComponent.init,
        getBlockFooterParent = BX.Sale.OrderAjaxComponent.getBlockFooter,
        editOrderParent = BX.Sale.OrderAjaxComponent.editOrder
    ;

    BX.namespace('BX.Sale.OrderAjaxComponentExt');

    BX.Sale.OrderAjaxComponentExt = BX.Sale.OrderAjaxComponent;
    BX.Sale.OrderAjaxComponentExt.selectDeliveryRayons = BX('#select_rayon_delivery');

    BX.Sale.OrderAjaxComponentExt.doSaveAction = function()
    {
        if (this.isOrderSaveAllowed())
        {

            ym(61642846,'reachGoal','zakaz');
            roistat.event.send('order_created');

            this.sendRequest('saveOrderAjax');
        }
    };
    BX.Sale.OrderAjaxComponentExt.editTotalBlock = function()
    {
        if (!this.totalInfoBlockNode || !this.result.TOTAL)
            return;

        var total = this.result.TOTAL,
            priceHtml, params = {},
            discText, valFormatted, i,
            curDelivery, deliveryError, deliveryValue,
            showOrderButton = this.params.SHOW_TOTAL_ORDER_BUTTON === 'Y';

        BX.cleanNode(this.totalInfoBlockNode);

        if (parseFloat(total.ORDER_PRICE) === 0)
        {
            priceHtml = this.params.MESS_PRICE_FREE;
            params.free = true;
        }
        else
        {
            priceHtml = total.ORDER_PRICE_FORMATED;
        }

        if (this.options.showPriceWithoutDiscount)
        {
            priceHtml += '<br><span class="bx-price-old">' + total.PRICE_WITHOUT_DISCOUNT + '</span>';
        }

        this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_SUMMARY'), priceHtml, params));

        if (this.options.showOrderWeight)
        {
           // this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_WEIGHT_SUM'), total.ORDER_WEIGHT_FORMATED));
        }

        if (this.options.showTaxList)
        {
            for (i = 0; i < total.TAX_LIST.length; i++)
            {
                valFormatted = total.TAX_LIST[i].VALUE_MONEY_FORMATED || '';
                this.totalInfoBlockNode.appendChild(
                    this.createTotalUnit(
                        total.TAX_LIST[i].NAME + (!!total.TAX_LIST[i].VALUE_FORMATED ? ' ' + total.TAX_LIST[i].VALUE_FORMATED : '') + ':',
                        valFormatted
                    )
                );
            }
        }

        params = {};
        curDelivery = this.getSelectedDelivery();
        deliveryError = curDelivery && curDelivery.CALCULATE_ERRORS && curDelivery.CALCULATE_ERRORS.length;

        if (deliveryError)
        {
            deliveryValue = BX.message('SOA_NOT_CALCULATED');
            params.error = deliveryError;
        }
        else
        {
            if (parseFloat(total.DELIVERY_PRICE) === 0)
            {
                deliveryValue = this.params.MESS_PRICE_FREE;
                params.free = true;
            }
            else
            {
                deliveryValue = total.DELIVERY_PRICE_FORMATED;
            }

            if (
                curDelivery && typeof curDelivery.DELIVERY_DISCOUNT_PRICE !== 'undefined'
                && parseFloat(curDelivery.PRICE) > parseFloat(curDelivery.DELIVERY_DISCOUNT_PRICE)
            )
            {
                deliveryValue += '<br><span class="bx-price-old">' + curDelivery.PRICE_FORMATED + '</span>';
            }
        }

        if (this.result.DELIVERY.length)
        {
            this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_DELIVERY'), deliveryValue, params));
        }

        if (this.options.showDiscountPrice)
        {
            discText = this.params.MESS_ECONOMY;
            if (total.DISCOUNT_PERCENT_FORMATED && parseFloat(total.DISCOUNT_PERCENT_FORMATED) > 0)
                discText += total.DISCOUNT_PERCENT_FORMATED;

            this.totalInfoBlockNode.appendChild(this.createTotalUnit(discText + ':', total.DISCOUNT_PRICE_FORMATED, {highlighted: true}));
        }

        if (this.options.showPayedFromInnerBudget)
        {
            this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_IT'), total.ORDER_TOTAL_PRICE_FORMATED));
            this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_PAYED'), total.PAYED_FROM_ACCOUNT_FORMATED));
            this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_LEFT_TO_PAY'), total.ORDER_TOTAL_LEFT_TO_PAY_FORMATED, {total: true}));
        }
        else
        {
            this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_IT'), total.ORDER_TOTAL_PRICE_FORMATED, {total: true}));
        }

        if (parseFloat(total.PAY_SYSTEM_PRICE) >= 0 && this.result.DELIVERY.length)
        {
            this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_PAYSYSTEM_PRICE'), '~' + total.PAY_SYSTEM_PRICE_FORMATTED));
        }

        if (!this.result.SHOW_AUTH)
        {
            this.totalInfoBlockNode.appendChild(
                BX.create('DIV', {
                    props: {className: 'bx-soa-cart-total-button-container' + (!showOrderButton ? ' visible-xs' : '')},
                    children: [
                        BX.create('A', {
                            props: {
                                href: 'javascript:void(0)',
                                className: 'btn btn-default btn-lg btn-order-save'
                            },
                            html: this.params.MESS_ORDER,
                            events: {
                                click: BX.proxy(this.clickOrderSaveAction, this)
                            }
                        })

                    ]
                })
            );
        }

        this.editMobileTotalBlock();
    };
    BX.Sale.OrderAjaxComponentExt.createDeliveryItemRayon = function()
    {

        var el = {
            ID: 0,
            NAME: "Доставка",
            OWN_NAME: "Доставка",
            SORT: 100,
            CHECKED: 'N',
        };

        var checked = false;
        for (var k = 1; k < this.result.DELIVERY.length; k++)
        {
            var params = {
                value: this.result.DELIVERY[k].ID,
                text: this.result.DELIVERY[k].NAME,
            };
            if(this.result.DELIVERY[k].CHECKED == 'Y'){
                el.CHECKED = 'Y';
                el.ID = this.result.DELIVERY[k].ID;
                params.selected = "selected";
            }
            el.SORT = this.result.DELIVERY[k].SORT;

        }

        var checked = el.CHECKED == 'Y',
            deliveryId = parseInt(el.ID),
            labelNodes = [
                BX.create('INPUT', {
                    props: {
                        id: 'ID_DELIVERY_ID_' + deliveryId,
                        name: 'DELIVERY_ID',
                        type: 'checkbox',
                        className: 'bx-soa-pp-company-checkbox',
                        value: deliveryId,
                        checked: checked
                    }
                })
            ],
            deliveryCached = this.deliveryCachedInfo[deliveryId],
            logotype, label, title, itemNode, logoNode;

        logoNode = BX.create('DIV', {props: {className: 'bx-soa-pp-company-image'}});
        logotype = this.getImageSources(el, 'LOGOTIP');
        if (logotype && logotype.src_2x)
        {
            logoNode.setAttribute('style',
                'background-image: url(' + logotype.src_1x + ');' +
                'background-image: -webkit-image-set(url(' + logotype.src_1x + ') 1x, url(' + logotype.src_2x + ') 2x)'
            );
        }
        else
        {
            logotype = logotype && logotype.src_1x || this.defaultDeliveryLogo;
            logoNode.setAttribute('style', 'background-image: url(' + logotype + ');');
        }
        labelNodes.push(logoNode);

        if (el.PRICE >= 0 || typeof el.DELIVERY_DISCOUNT_PRICE !== 'undefined')
        {
            labelNodes.push(
                BX.create('DIV', {
                    props: {className: 'bx-soa-pp-delivery-cost'},
                    html: typeof el.DELIVERY_DISCOUNT_PRICE !== 'undefined'
                        ? el.DELIVERY_DISCOUNT_PRICE_FORMATED
                        : el.PRICE_FORMATED})
            );
        }
        else if (deliveryCached && (deliveryCached.PRICE >= 0 || typeof deliveryCached.DELIVERY_DISCOUNT_PRICE !== 'undefined'))
        {
            labelNodes.push(
                BX.create('DIV', {
                    props: {className: 'bx-soa-pp-delivery-cost'},
                    html: typeof deliveryCached.DELIVERY_DISCOUNT_PRICE !== 'undefined'
                        ? deliveryCached.DELIVERY_DISCOUNT_PRICE_FORMATED
                        : deliveryCached.PRICE_FORMATED})
            );
        }

        label = BX.create('DIV', {
            props: {
                className: 'bx-soa-pp-company-graf-container'
                    + (el.CALCULATE_ERRORS || deliveryCached && deliveryCached.CALCULATE_ERRORS ? ' bx-bd-waring' : '')},
            children: labelNodes
        });

        if (this.params.SHOW_DELIVERY_LIST_NAMES == 'Y')
        {
            title = BX.create('DIV', {
                props: {className: 'bx-soa-pp-company-smalltitle'},
                text: this.params.SHOW_DELIVERY_PARENT_NAMES != 'N' ? el.NAME : el.OWN_NAME
            });
        }

        itemNode = BX.create('DIV', {
            props: {className: 'bx-soa-pp-company col-lg-4 col-sm-4 col-xs-6','id':'blockDeliveryToRayons'},
            children: [label, title],
            events: {click: BX.proxy(this.showDeliveryRayonsSelect, this)}
        });
        checked && BX.addClass(itemNode, 'bx-selected');

        if (checked && this.result.LAST_ORDER_DATA.PICK_UP)
            this.lastSelectedDelivery = deliveryId;

        return itemNode;
    };

    BX.Sale.OrderAjaxComponentExt.showBlockDeliveryRayonsSelect = function(el)
    {

        var parBlock = BX('blockDeliveryList');

        var   select =   BX.create('SELECT', {
            props: {
                id: 'DELIVERY_RAYONS',
                name: 'DELIVERY_ID',
                className: 'bx-soa-pp-company-select select-delivery form-control'
            }});

        var checked = false;
        var params = {
            value: 0,
            text: "Выберите район",
        };

        var  deliveryItem =  BX.create('OPTION', {
            props: params});

        BX.prepend(deliveryItem, select);
        for (var k = 1; k < this.result.DELIVERY.length; k++)
        {
            var params = {
                value: this.result.DELIVERY[k].ID,
                text: this.result.DELIVERY[k].NAME,
            };
            if(this.result.DELIVERY[k].CHECKED == 'Y'){
                el.CHECKED = 'Y';
                params.selected = "selected";
            }
            el.SORT = this.result.DELIVERY[k].SORT;
            var  deliveryItem =  BX.create('OPTION', {
                props: params});
            BX.prepend(deliveryItem, select);
        }

        var   newBlockForSelect =   BX.create('DIV', {
            props: {
                className: 'col-lg-12 col-sm-12 col-xs-12'
            },
            html: "<span>Выберите район доставки</span>"
        });

        BX.append(select, newBlockForSelect);
        $(parBlock).append(newBlockForSelect);

        var textForDelivery = '<div class="col-lg-12 col-sm-12 col-xs-12 freeDelivery">' +
            '<b>Бесплатная доставка по районам возможна при минимальной сумме заказа:</b><br />' +
            '<p>Калининский район: 2000 руб.</p>' +
            '<p>Заельцовский район: 2000 руб.</p>' +
            '<p>Дзержинский район: 2000 руб.</p>' +
            '<p>Железнодорожный район: 1500 руб.</p>' +
            '<p>Октябрьский район: 2000 руб.</p>' +
            '<p>Центральный район: 1500 руб.</p>' +
            '<p>Ленинский район: 2500 руб.</p>' +
            '<p>Кировский район: 3000 руб.</p>' +
            '<p>Первомайский район: 5000 руб.</p>' +
            '<p>Советский район: 5000 руб.</p>' +
            '</div>';

        $(parBlock).append(textForDelivery);

        $('#DELIVERY_RAYONS').on('change',function(){
            BX.Sale.OrderAjaxComponentExt.changedDeliveryOnSelect(this);
        });
    };


    BX.Sale.OrderAjaxComponentExt.alterDateProperty = function(settings, inputText)
    {
        var parentNode = BX.findParent(inputText, {tagName: 'DIV'}),
            addon;

        BX.addClass(parentNode, 'input-group');
        addon = BX.create('DIV', {
            props: {className: 'input-group-addon calenderDelivery'},
            children: [BX.create('I', {props: {className: 'bx-calendar'}})]
        });
        BX.insertAfter(addon, inputText);
        BX.remove(parentNode.querySelector('input[type=button]'));
        BX.bind(addon, 'click', BX.delegate(function(e){
            var target = e.target || e.srcElement,
                parentNode = BX.findParent(target, {tagName: 'DIV', className: 'input-group'});

            BX.calendar({
                node: parentNode.querySelector('.input-group-addon'),
                field: parentNode.querySelector('input[type=text]').name,
                form: '',
                bTime: settings.TIME == 'Y',
                bHideTime: false
            });
        }, this));
    };

    BX.Sale.OrderAjaxComponentExt.showDeliveryRayonsSelect = function(el)
    {
       $('#bx-soa-delivery').find('.bx-soa-pp-company').removeClass('bx-selected');
       var delBlocks = $('#bx-soa-delivery').find('input');

       for(var i=0;i<delBlocks.length; i++ ){
           delBlocks[i].checked = false;
       }

        var blocks = BX('blockDeliveryToRayons');

        var input = $(blocks).find('input');

        if(input){
            $(input).prop('checked', true);
        }

       if(!BX('DELIVERY_RAYONS')){
           BX.Sale.OrderAjaxComponentExt.showBlockDeliveryRayonsSelect(el);
       }
        BX.addClass(blocks,'bx-selected');
    };


    //form list of delivery services
    BX.Sale.OrderAjaxComponentExt.editDeliveryItems = function(deliveryNode)
    {
        if (!this.result.DELIVERY || this.result.DELIVERY.length <= 0)
            return;

        var deliveryItemsContainer = BX.create('DIV', {props: {className: 'col-sm-7 bx-soa-pp-item-container',id: 'blockDeliveryList'}}),
            deliveryItemNode, k;

        deliveryItemNode = this.createDeliveryItem(this.result.DELIVERY[0]); //первая служба - Самовывоз
        deliveryItemsContainer.appendChild(deliveryItemNode);

        deliveryItemNode = this.createDeliveryItemRayon();
        deliveryItemsContainer.appendChild(deliveryItemNode);

        deliveryNode.appendChild(deliveryItemsContainer);

    };

    BX.Sale.OrderAjaxComponentExt.selectDelivery = function(event)
    {
        if (!this.orderBlockNode)
            return;

        var target = event.target || event.srcElement,
            actionSection =  BX.hasClass(target, 'bx-soa-pp-company') ? target : BX.findParent(target, {className: 'bx-soa-pp-company'}),
            selectedSection = this.deliveryBlockNode.querySelector('.bx-soa-pp-company.bx-selected'),
            actionInput, selectedInput;


        if (BX.hasClass(actionSection, 'bx-selected'))
            return BX.PreventDefault(event);

        if (actionSection)
        {

            if(actionSection.getAttribute('id') !== 'blockDeliveryToRayons'){
                var removePar = BX.findParent(BX('DELIVERY_RAYONS'));
                if(removePar){
                    BX.remove(removePar);
                }
            }

            actionInput = actionSection.querySelector('input[type=checkbox]');
            BX.addClass(actionSection, 'bx-selected');
            actionInput.checked = true;
        }
        if (selectedSection)
        {
            selectedInput = selectedSection.querySelector('input[type=checkbox]');
            BX.removeClass(selectedSection, 'bx-selected');
            selectedInput.checked = false;
        }

        this.sendRequest();
    };

    BX.Sale.OrderAjaxComponentExt.changedDeliveryOnSelect = function(radio){
        var id = $(radio).val();

        var delivery = BX.Sale.OrderAjaxComponentExt.getSelectedDeliveryOnSelect(id);

        var actionSection =    BX.findParent(radio, {className: 'bx-soa-pp-company'}),
            selectedSection =  BX.Sale.OrderAjaxComponentExt.deliveryBlockNode.querySelector('.bx-soa-pp-company.bx-selected'),
            actionInput, selectedInput;

         BX.Sale.OrderAjaxComponentExt.sendRequest();
    };


    BX.Sale.OrderAjaxComponentExt.checkSelectRayons= function()
    {
        var blocks = BX('blockDeliveryToRayons');
        if(BX.hasClass(blocks,'bx-selected')){

            var el = {
                ID: 0,
                NAME: "Доставка",
                OWN_NAME: "Доставка",
                SORT: 100,
                CHECKED: 'N',
            };

            var checked = false;
            for (var k = 1; k < this.result.DELIVERY.length; k++)
            {
                var params = {
                    value: this.result.DELIVERY[k].ID,
                    text: this.result.DELIVERY[k].NAME,
                };
                if(this.result.DELIVERY[k].CHECKED == 'Y'){
                    el.CHECKED = 'Y';
                    el.ID = this.result.DELIVERY[k].ID;
                    params.selected = "selected";
                }
                el.SORT = this.result.DELIVERY[k].SORT;

            }
            BX.Sale.OrderAjaxComponentExt.showBlockDeliveryRayonsSelect(el);
        }

    };


    BX.Sale.OrderAjaxComponentExt.getSelectedDeliveryOnSelect= function()
    {
        var deliveryCheckbox = this.deliveryBlockNode.querySelector('input[type=checkbox][name=DELIVERY_ID]:checked'),
            currentDelivery = false,
            deliveryId, i;

        if (!deliveryCheckbox)
            deliveryCheckbox = this.deliveryHiddenBlockNode.querySelector('input[type=checkbox][name=DELIVERY_ID]:checked');

        if (!deliveryCheckbox)
            deliveryCheckbox = this.deliveryHiddenBlockNode.querySelector('input[type=hidden][name=DELIVERY_ID]');

        if (deliveryCheckbox)
        {
            deliveryId = deliveryCheckbox.value;

            for (i in this.result.DELIVERY)
            {
                if (this.result.DELIVERY[i].ID == deliveryId)
                {
                    currentDelivery = this.result.DELIVERY[i];
                    break;
                }
            }
        }

        return currentDelivery;
    };


    BX.Sale.OrderAjaxComponentExt.getAllFormData = function()
    {
        var form = BX('bx-soa-order-form'),
            prepared = BX.ajax.prepareForm(form),
            i;

        for (i in prepared.data)
        {
            if (prepared.data.hasOwnProperty(i) && i == '')
            {
                delete prepared.data[i];
            }
        }

        return !!prepared && prepared.data ? prepared.data : {};
    };
    BX.Sale.OrderAjaxComponentExt.editActiveDeliveryBlock = function(activeNodeMode)
    {


        var node = activeNodeMode ? this.deliveryBlockNode : this.deliveryHiddenBlockNode,
            deliveryContent, deliveryNode;

        if (this.initialized.delivery)
        {
            BX.remove(BX.lastChild(node));
            node.appendChild(BX.firstChild(this.deliveryHiddenBlockNode));
        }
        else
        {
            deliveryContent = node.querySelector('.bx-soa-section-content');
            if (!deliveryContent)
            {
                deliveryContent = this.getNewContainer();
                node.appendChild(deliveryContent);
            }
            else
                BX.cleanNode(deliveryContent);

            this.getErrorContainer(deliveryContent);

            deliveryNode = BX.create('DIV', {props: {className: 'bx-soa-pp row'}});

            this.editDeliveryItems(deliveryNode);

            deliveryContent.appendChild(deliveryNode);

            this.checkSelectRayons();

            this.editDeliveryInfo(deliveryNode);

            if (this.params.SHOW_COUPONS_DELIVERY == 'Y')
                this.editCoupons(deliveryContent);

            this.getBlockFooter(deliveryContent);

            $('#DELIVERY_RAYONS').on('change',function(){
                BX.Sale.OrderAjaxComponentExt.changedDeliveryOnSelect(this);
            });

        }
    };


    BX.bind(BX('#select_rayon_delivery'), 'click', BX.proxy(BX.Sale.OrderAjaxComponentExt.editDeliveryEclair, this));


    BX.Sale.OrderAjaxComponentExt.getSelectedDeliveryOnSelect = function(){
        var deliveryId = $('#DELIVERY_RAYONS').val();
        return deliveryId;
    };


    BX.Sale.OrderAjaxComponentExt.editDeliveryInfoSelection = function(deliveryNode)
    {
        if (!this.result.DELIVERY)
            return;

        var deliveryInfoContainer = BX.create('DIV', {props: {className: 'col-sm-5 bx-soa-pp-desc-container'}}),
            currentDelivery, logotype, name, logoNode,
            subTitle, label, title, price, period,
            clear, infoList, extraServices, extraServicesNode;

        BX.cleanNode(deliveryInfoContainer);
        currentDelivery = this.getSelectedDeliveryOnSelect();
        logoNode = BX.create('DIV', {props: {className: 'bx-soa-pp-company-image'}});
        $(logoNode).html('Доставка курьером по районам');

        name = this.params.SHOW_DELIVERY_PARENT_NAMES != 'N' ? currentDelivery.NAME : currentDelivery.OWN_NAME;

        if (this.params.SHOW_DELIVERY_INFO_NAME == 'Y')
            subTitle = BX.create('DIV', {props: {className: 'bx-soa-pp-company-subTitle'}, text: name});

        title = BX.create('DIV', {
            props: {className: 'bx-soa-pp-company-block'},
            children: [
                BX.create('DIV', {props: {className: 'bx-soa-pp-company-desc'}, html: currentDelivery.DESCRIPTION}),
                currentDelivery.CALCULATE_DESCRIPTION
                    ? BX.create('DIV', {props: {className: 'bx-soa-pp-company-desc'}, html: currentDelivery.CALCULATE_DESCRIPTION})
                    : null
            ]
        });

        if (currentDelivery.PRICE >= 0)
        {
            price = BX.create('LI', {
                children: [
                    BX.create('DIV', {
                        props: {className: 'bx-soa-pp-list-termin'},
                        html: this.params.MESS_PRICE + ':'
                    }),
                    BX.create('DIV', {
                        props: {className: 'bx-soa-pp-list-description'},
                        children: this.getDeliveryPriceNodes(currentDelivery)
                    })
                ]
            });
        }

        if (currentDelivery.PERIOD_TEXT && currentDelivery.PERIOD_TEXT.length)
        {
            period = BX.create('LI', {
                children: [
                    BX.create('DIV', {props: {className: 'bx-soa-pp-list-termin'}, html: this.params.MESS_PERIOD + ':'}),
                    BX.create('DIV', {props: {className: 'bx-soa-pp-list-description'}, html: currentDelivery.PERIOD_TEXT})
                ]
            });
        }

        clear = BX.create('DIV', {style: {clear: 'both'}});
        infoList = BX.create('UL', {props: {className: 'bx-soa-pp-list'}, children: [price, period]});
        extraServices = this.getDeliveryExtraServices(currentDelivery);

        if (extraServices.length)
        {
            extraServicesNode = BX.create('DIV', {
                props: {className: 'bx-soa-pp-company-block'},
                children: extraServices
            });
        }

        deliveryInfoContainer.appendChild(
            BX.create('DIV', {
                props: {className: 'bx-soa-pp-company'},
                children: [subTitle, label, title, clear, extraServicesNode, infoList]
            })
        );
        deliveryNode.appendChild(deliveryInfoContainer);

        if (this.params.DELIVERY_NO_AJAX != 'Y')
            this.deliveryCachedInfo[currentDelivery.ID] = currentDelivery;
    };


    BX.Sale.OrderAjaxComponentExt.changedDeliveryRayon = function(radio){
        var delivery = BX.Sale.OrderAjaxComponentExt.getSelectedDelivery();
        var price = $(radio).val();
        var id = $(radio).data('id');

        BX.setCookie('eclairDelivery', JSON.stringify({
            delivery: id,
            value: price
        }), { expires: 3600, path: '/' });
        BX.Sale.OrderAjaxComponentExt.sendRequest();
    };



    BX.Sale.OrderAjaxComponentExt.editPaySystemInfo = function(paySystemNode){
        if (!this.result.PAY_SYSTEM || (this.result.PAY_SYSTEM.length == 0 && this.result.PAY_FROM_ACCOUNT != 'Y'))
            return;

        var paySystemInfoContainer = BX.create('DIV', {
                props: {
                    className: (this.result.PAY_SYSTEM.length == 0 ? 'col-sm-12' : 'col-sm-5') + ' bx-soa-pp-desc-container'
                }
            }),
            innerPs, extPs, delimiter, currentPaySystem,
            logotype, logoNode, subTitle, label, title, price;

        BX.cleanNode(paySystemInfoContainer);

        if (this.result.PAY_FROM_ACCOUNT == 'Y')
            innerPs = this.getInnerPaySystem(paySystemInfoContainer);

        currentPaySystem = this.getSelectedPaySystem();
        if (currentPaySystem)
        {
            logoNode = BX.create('DIV', {props: {className: 'bx-soa-pp-company-image'}});
            logotype = this.getImageSources(currentPaySystem, 'PSA_LOGOTIP');
            if (logotype && logotype.src_2x)
            {
                logoNode.setAttribute('style',
                    'background-image: url(' + logotype.src_1x + ');' +
                    'background-image: -webkit-image-set(url(' + logotype.src_1x + ') 1x, url(' + logotype.src_2x + ') 2x)'
                );
            }
            else
            {
                logotype = logotype && logotype.src_1x || this.defaultPaySystemLogo;
                logoNode.setAttribute('style', 'background-image: url(' + logotype + ');');
            }

            if (this.params.SHOW_PAY_SYSTEM_INFO_NAME == 'Y')
            {
                subTitle = BX.create('DIV', {
                    props: {className: 'bx-soa-pp-company-subTitle'},
                    text: currentPaySystem.NAME
                });
            }

            label = BX.create('DIV', {
                props: {className: 'bx-soa-pp-company-logo'},
            });

            title = BX.create('DIV', {
                props: {className: 'bx-soa-pp-company-block'},
                children: [BX.create('DIV', {props: {className: 'bx-soa-pp-company-desc'}, html: currentPaySystem.DESCRIPTION})]
            });

            if (currentPaySystem.PRICE && parseFloat(currentPaySystem.PRICE) > 0)
            {
                price = BX.create('UL', {
                    props: {className: 'bx-soa-pp-list'},
                    children: [
                        BX.create('LI', {
                            children: [
                                BX.create('DIV', {props: {className: 'bx-soa-pp-list-termin'}, html: this.params.MESS_PRICE + ':'}),
                                BX.create('DIV', {props: {className: 'bx-soa-pp-list-description'}, text: '~' + currentPaySystem.PRICE_FORMATTED})
                            ]
                        })
                    ]
                });
            }

            extPs = BX.create('DIV', {children: [subTitle, label, title, price]});
        }

        if (innerPs && extPs)
            delimiter = BX.create('HR', {props: {className: 'bxe-light'}});

        paySystemInfoContainer.appendChild(
            BX.create('DIV', {
                props: {className: 'bx-soa-pp-company'},
                children: [innerPs, delimiter, extPs]
            })
        );
        paySystemNode.appendChild(paySystemInfoContainer);
    };
    BX.Sale.OrderAjaxComponentExt.showWarnings = function()
    {
        var sections = this.orderBlockNode.querySelectorAll('div.bx-soa-section.bx-active'),
            currentDelivery = this.getSelectedDelivery(),
            k,  warningString;

        for (k = 0; k < sections.length; k++)
        {
            BX.removeClass(sections[k], 'bx-step-warning');

            if (sections[k].getAttribute('data-visited') == 'false')
                BX.removeClass(sections[k], 'bx-step-completed');
        }

        if (!this.result.WARNING || !this.options.showWarnings)
            return;

        for (k in this.result.WARNING)
        {
            if (this.result.WARNING.hasOwnProperty(k))
            {
                switch (k.toUpperCase())
                {
                    case 'DELIVERY':
                        if (this.deliveryBlockNode.getAttribute('data-visited') === 'true')
                        {
                            this.showBlockWarning(this.deliveryBlockNode, this.result.WARNING[k], true);
                            this.showBlockWarning(this.deliveryHiddenBlockNode, this.result.WARNING[k], true);
                        }

                        break;
                    case 'PAY_SYSTEM':
                        if (this.paySystemBlockNode.getAttribute('data-visited') === 'true')
                        {
                            this.showBlockWarning(this.paySystemBlockNode, this.result.WARNING[k], true);
                            this.showBlockWarning(this.paySystemHiddenBlockNode, this.result.WARNING[k], true);
                        }

                        break;
                }
            }
        }
    };
})();