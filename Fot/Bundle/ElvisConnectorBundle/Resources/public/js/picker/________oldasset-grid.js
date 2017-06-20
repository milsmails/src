'use strict';

define(
    [
        'jquery',
        'underscore',
        'backbone',
        'routing',
        'pim/form',
        'text!fot/template/picker/asset-grid',
        'text!fot/template/picker/basket',
        'oro/datagrid-builder',
        'oro/mediator',
        'pim/fetcher-registry',
        'pim/user-context',
        'oro/datafilter/product_category-filter',
        'oro/translator'
    ],
    function ($,
              _,
              Backbone,
              Routing,
              BaseForm,
              template,
              basketTemplate,
              datagridBuilder,
              mediator,
              FetcherRegistry,
              UserContext,
              CategoryFilter,__ ) {
        return BaseForm.extend({
            template: _.template(template),
            basketTemplate: _.template(basketTemplate),
            events: {
                'click .remove-asset': 'removeAssetFromBasket'
            },
            currentTreeId : "",
            /**
             * {@inheritdoc}
             */
            initialize: function () {
                this.datagridModel = null;
                BaseForm.prototype.initialize.apply(this, arguments);
            },

            /**
             * {@inheritdoc}
             */
            configure: function () {
                this.datagrid = {
                    name: 'asset-picker-grid',
                    paramName: 'assetCodes'
                };

                mediator.on('datagrid:selectModel:' + this.datagrid.name, this.selectModel.bind(this));
                mediator.on('datagrid:unselectModel:' + this.datagrid.name, this.unselectModel.bind(this));
                mediator.on('datagrid_collection_set_after', this.updateChecked.bind(this));
                mediator.on('datagrid_collection_set_after', this.setDatagrid.bind(this));
                mediator.on('grid_load:complete', this.updateChecked.bind(this));
                mediator.once('column_form_listener:initialized', function onColumnListenerReady(gridName) {
                    if (!this.configured) {
                        mediator.trigger(
                            'column_form_listener:set_selectors:' + gridName,
                            {included: '#asset-appendfield'}
                        );
                    }
                }.bind(this));

                return BaseForm.prototype.configure.apply(this, arguments);
            },

            /**
             * {@inheritdoc}
             */
            setCurrentTreeId: function(id) {
                this.currentTreeId = id;
            },
            render: function () {
                if (!this.configured) {
                    return this;
                }

                this.$el.html(this.template({
                    locale: this.getLocale()
                }));
                this.renderTree();
                this.renderGrid(this.datagrid);
                return this.renderExtensions();

            },

            /**
             * Render the asset grid
             */
            renderGrid: function () {
                var urlParams = {
                    alias: this.datagrid.name,
                    id : this.currentTreeId,
                    params: {
                        dataLocale: this.getLocale(),
                        _filter: {
                            category: {value: {categoryId: -2}}, // -2 = all categories
                            scope: {value: this.getScope()}
                        }
                    }
                };

                $.get(Routing.generate('elvis_connector_get_categorie_content', urlParams)).done(function (response) {
                    //console.log(JSON.stringify(response));
                    this.$('#grid-' + this.datagrid.name).data(response  );

                    require(response.metadata.requireJSModules, function () {
                        datagridBuilder(_.toArray(arguments));
                    });
                }.bind(this));
            },

            /**
             * Triggered by the event 'datagrid_collection_set_after' to keep a locale reference to
             * the grid model #gridCrap
             *
             * @param {Object} datagridModel
             */
            setDatagrid: function (datagridModel) {
                this.datagridModel = datagridModel;
            },

            /**
             * Triggered by the datagrid:selectModel:asset-picker-grid event
             *
             * @param {Object} model
             */
            selectModel: function (model) {
                this.addAsset(model.get('code'));
            },
            testEvent: function (msg) {
                console.log(JSON.stringify(msg));
            },

            /**
             * Triggered by the datagrid:unselectModel:asset-picker-grid event
             *
             * @param {Object} model
             */
            unselectModel: function (model) {
                this.removeAsset(model.get('code'));
            },

            /**
             * Add an asset to the basket
             *
             * @param {string} code
             *
             * @return this
             */
            addAsset: function (code) {
                var assets = this.getAssets();
                assets.push(code);
                assets = _.uniq(assets);

                this.setAssets(assets);

                return this;
            },

            /**
             * Remove an asset from the collection
             *
             * @param {string} code
             *
             * @return this
             */
            removeAsset: function (code) {
                var assets = _.without(this.getAssets(), code);

                this.setAssets(assets);

                return this;
            },

            /**
             * Get all assets in the collection
             *
             * @return {Array}
             */
            getAssets: function () {
                var assets = $('#asset-appendfield').val();

                return (!_.isUndefined(assets) && '' !== assets) ? assets.split(',') : [];
            },

            /**
             * Set assets
             *
             * @param {Array} assetCodes
             *
             * @return this
             */
            setAssets: function (assetCodes) {
                if(assetCodes) $('#asset-appendfield').val(assetCodes.join(','));
                this.updateBasket();

                return this;
            },

            /**
             * Update the checked rows in the grid according to the current model
             *
             * @param {Object} datagrid
             */
            updateChecked: function (datagrid) {
                var assets = this.getAssets();

                _.each(datagrid.models, function (row) {
                    if (_.contains(assets, row.get('code'))) {
                        row.set('is_checked', true);
                    } else {
                        row.set('is_checked', null);
                    }
                }.bind(this));

                this.setAssets(assets);
            },

            /**
             * Remove an asset from the basket (triggered by 'click .remove-asset')
             *
             * @param {Event} event
             */
            removeAssetFromBasket: function (event) {
                this.removeAsset(event.currentTarget.dataset.asset);
                if (this.datagridModel) {
                    this.updateChecked(this.datagridModel);
                }
            },

            /**
             * Render the basket to update its content
             */
            updateBasket: function () {
                FetcherRegistry.getFetcher('asset').fetchByIdentifiers(this.getAssets())
                    .then(function (assets) {
                        this.$('.basket').html(this.basketTemplate({
                            assets: assets,
                            thumbnailFilter: 'thumbnail',
                            scope: this.getScope(),
                            locale: this.getLocale()
                        }));

                        this.delegateEvents();
                    }.bind(this));
            },

            /**
             * Get the current locale
             *
             * @return {string}
             */
            getLocale: function () {
                return UserContext.get('catalogLocale');
            },

            /**
             * Get the current scope
             *
             * @return {string}
             */
            getScope: function () {
                return UserContext.get('catalogScope');
            },
            getTreeConfig: function () {
                var unclassified      = -1;
                var all               = -2;
                var selectedNode      = '';
                var selectedTree      = '';
                var includeSub        = false;
                var dataLocale        = null;
                var relatedEntity     = null;
                var $el               = null;
                var categoryBaseRoute = '';

                var getNodeId = function (node) {
                    var nodeId = (node && node.attr && node.attr('id')) ? node.attr('id').replace('node_', '') : '';
                    return nodeId;
                };
                var getActiveNode = function (skipVirtual) {
                    return !!selectedNode ? selectedNode : selectedTree;
                };

                return {
                    core: {
                        animation: 200,
                        strings: {loading: _.__('jstree.loading')}
                    },
                    plugins: [
                        'tree_selector',
                        'nested_switch',
                        'themes',
                        'json_data',
                        'ui',
                        'crrm',
                        'types'
                    ],
                    nested_switch: {
                        state: includeSub,
                        label: __('jstree.include_sub'),
                        callback: function (state) {
                            includeSub = state;

                            $el.jstree('instance').data.tree_selector.ajax.url = Routing.generate('elvis_connector_get_categorie_roots');
                            $el.jstree('refresh');
                            $el.trigger('after_tree_loaded.jstree');
                            triggerUpdate();
                        }

                    },
                    tree_selector: {
                        ajax: {
                            'url': Routing.generate('elvis_connector_get_categorie_roots')
                        },
                        auto_open_root: true,
                        node_label_field: 'label',
                        preselect_node_id: getActiveNode()
                    },
                    themes: {
                        dots: true,
                        icons: true
                    },
                    json_data: {
                        ajax: {
                            url: Routing.generate('elvis_connector_get_categorie'),
                            data: function (node) {
                                //alert(getNodeId(node));
                                return {
                                    id: getNodeId(node),
                                    select_node_id: getActiveNode(),
                                    with_items_count: 1,
                                    include_sub: +includeSub
                                };
                            }/*,
                             success: function (data) {
                             console.log(JSON.stringify(data));
                             }*/
                        }
                    },
                    types: {
                        max_depth: -2,
                        max_children: -2,
                        valid_children: ['folder'],
                        types: {
                            'default': {
                                valid_children: 'folder'
                            }
                        }
                    },
                    ui: {
                        select_limit: 1,
                        select_multiple_modifier: false
                    }
                };
            }
            ,
            renderTree: function () {
                //$('#asset-tree').bind("select_node.jstree",this.testEvent($('#asset-tree').jstree('get_selected').attr('id'))).jstree(this.getTreeConfig());
                $('#asset-tree').jstree(this.getTreeConfig());
            }
        });
    }
);
