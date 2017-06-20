'use strict';

define([
        'pim/field',
        'underscore',
        'text!fot/template/picker/elvis-asset-collection',
        'pim/form-builder',
        'routing',
        'oro/loading-mask'
    ], function (Field,
                 _,
                 fieldTemplate,
                 FormBuilder,
                 Routing, LoadingMask) {
        var model,
            modalBox;
        var AssetSelectionView = Field.extend({
            productAttributes: {}, // wird über product-info.js injected
            fieldTemplate: _.template(fieldTemplate),
            events: {
                'change input[name=asset-sids]': 'updateModel',
                'click .add-asset': 'openModal'
            },
            initialize: function (attribute) {
                model = this;
                AssetSelectionView.__super__.initialize.apply(this, arguments);
            },
            renderInput: function (context) {
                this.currentRenderContext = context;

                var assets = [];
                if (!context.value.data)
                    context.value.data = "";
                _.each(context.value.data.split(','), function (item) {
                    if (!item) return;
                    assets.push({
                        id: item,
                        url: Routing.generate(
                            'eikona_tessa_media_show',
                            {assetId: item}
                        )
                    })
                });
                return this.fieldTemplate({
                    value: context.value,
                    assets: assets
                });
            },
            updateModel: function () {
            },
            openModal: function () {

                var deferred = $.Deferred();

                FormBuilder.build('fotelvis-product-asset-picker-form').then(function (form) {
                    var modal = new Backbone.BootstrapModal({
                        className: 'modal modal-large',
                        modalOptions: {
                            backdrop: 'static',
                            keyboard: false
                        },
                        allowCancel: true,
                        okCloses: false,
                        title: _.__('pimee_product_asset.form.product.asset.manage_asset.title'),
                        content: '',
                        cancelText: _.__('pimee_product_asset.form.product.asset.manage_asset.cancel'),
                        okText: _.__('pimee_product_asset.form.product.asset.manage_asset.confirm')
                    });

                    modal.open();
                    modal.$el.addClass('modal-asset');
                    form.setElement(modal.$('.modal-body'))
                        .render()
                        .setAssets(this.data);

                    modal.on('cancel', deferred.reject);
                    modal.on('ok', function () {
                        var assets = _.sortBy(form.getAssets(), 'code');
                        modal.close();

                        deferred.resolve(assets);
                    }.bind(this));
                }.bind(this));

                return deferred.promise();
            }
        });
        return AssetSelectionView;
    }
);