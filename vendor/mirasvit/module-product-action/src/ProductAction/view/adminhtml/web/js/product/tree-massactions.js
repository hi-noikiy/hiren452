define([
    'Magento_Ui/js/grid/tree-massactions',
    'mageUtils',
    'uiRegistry',
    'underscore'
], function (TreeMassActions, utils, registry, _) {
    return TreeMassActions.extend({

        _getCallback: function (action, selections) {
            let callback = action.callback,
                args     = [action, selections];

            if (utils.isObject(callback)) {
                args.unshift(callback.target);

                callback = registry.async(callback.provider);
            } else if (_.isArray(callback)) {
                return function () {
                    _.each(callback, function (action) {
                        this.applyCallbackAction(action);
                    }.bind(this));
                }.bind(this);
            } else if (typeof callback != 'function') {
                callback = this.defaultCallback.bind(this);
            }

            return function () {
                callback.apply(null, args);
            };
        },

        applyCallbackAction: function (action) {
            const targetName = action.targetName,
                  params     = utils.copy(action.params) || [],
                  actionName = action.actionName;

            const target = registry.async(targetName);

            if (target && typeof target === 'function' && actionName) {
                params.unshift(actionName);
                target.apply(target, params);

                const targetFormName = targetName + '.mst_product_action_form_loader';
                const targetForm = registry.get(targetFormName);

                if (targetForm) {
                    targetForm.destroyInserted();
                    targetForm.render();
                }
            }
        }
    });
});
