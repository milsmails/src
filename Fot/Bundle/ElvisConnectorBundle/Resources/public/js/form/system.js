/*
 * system.js
 *
 * @author    Matthias Mahler <m.mahler@eikona.de>
 * @copyright 2017 Eikona AG (http://www.eikona.de)
 */

"use strict";

define([
        'underscore',
        'oro/translator',
        'routing',
        'pim/form',
        'text!fot/elvisconnector/templates/system/group/configuration'
    ],
    function (_,
              __,
              Routing,
              BaseForm,
              template) {
        return BaseForm.extend({
            className: 'AknFormContainer AknFormContainer--withPadding',
            events: {
                'change .elvis-config': 'updateModel'
            },
            isGroup: true,
            label: __('elvis.configuration.tab.label'),
            template: _.template(template),

            /**
             * {@inheritdoc}
             */
            render: function () {
                this.$el.html(this.template({
                    base_url: this.getFormData()['pim_fot_bundle_elvisconnector___base_url'] ?
                        this.getFormData()['pim_fot_bundle_elvisconnector___base_url'].value : '',
                    username: this.getFormData()['pim_fot_bundle_elvisconnector___username'] ?
                        this.getFormData()['pim_fot_bundle_elvisconnector___username'].value : '',
                    pwd: this.getFormData()['pim_fot_bundle_elvisconnector___pwd'] ?
                        this.getFormData()['pim_fot_bundle_elvisconnector___pwd'].value : '',
                }));


                this.delegateEvents();

                return BaseForm.prototype.render.apply(this, arguments);
            },

            /**
             * Update model after value change
             *
             * @param {Event}
             */
            updateModel: function (event) {
                var name = event.target.name;
                var data = this.getFormData();
                var newValue = event.target.value;
                if (name in data) {
                    data[name].value = newValue;
                } else {
                    data[name] = {value: newValue};
                }
                this.setData(data);
            }
        });
    }
);