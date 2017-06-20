'use strict';

/**
 * Asset collection field
 *
 * @author    Julien Sanchez <julien@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 */
define(
    [
        'jquery',
        'underscore',
        'backbone',
        'text!fot/template/picker/elvis-asset-collection',
        'pim/fetcher-registry',
        'pim/form-builder',
        'backbone/bootstrap-modal',

    ],
    function (
        $,
        _,
        Backbone,
        template,
        FetcherRegistry,
        FormBuilder
    ) {
        return Backbone.View.extend({
            className: 'AknAssetCollectionField',
            data: "",
            context: {},
            template: _.template(template),
            events: {
                'click .add-asset': 'updateAssets'
            },

            /**
             * {@inheritdoc}
             */
            render: function () {
                console.log( "render asset collection" +JSON.stringify(this.data));
                $.get(Routing.generate('elvis_connector_assets_product', {assets : this.data})).then(function (assets) {
                    console.log( JSON.stringify(assets));
                    this.$el.html(this.template({
                        assets: assets,
                        locale: this.context.locale,
                        scope: this.context.scope,
                        thumbnailFilter: 'thumbnail',
                        editMode: this.context.editMode
                    }));

                    this.delegateEvents();
                }.bind(this));

                return this;
            },

            /**
             * Set data into the view
             *
             * @param {Array} data
             */
            setData: function (data) {
                this.data = data;
            },

            /**
             * Set context into the view
             *
             * @param {Object} context
             */
            setContext: function (context) {
                this.context = context;
            },

            /**
             * Launch the asset picker and set the assets after update
             */
            updateAssets: function () {
                this.manageAssets().then(function (assets) {
                    this.data = assets;

                    this.trigger('collection:change', assets);
                    this.render();
                }.bind(this));
            },

            /**
             * Launch the asset picker
             *
             * @return {Promise}
             */
            manageAssets: function () {
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
                        var assets = form.getAssets().join(',');
                        console.log("manageAssets this.data :" +JSON.stringify(assets));
                        modal.close();
                        console.log("manageAssets assets :" +JSON.stringify(assets));
                        deferred.resolve(assets);
                    }.bind(this));
                }.bind(this));

                return deferred.promise();
            }
        });
    }
);
