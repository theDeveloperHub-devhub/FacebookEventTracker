
define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (jQuery, ko, Component, customerData) {
    'use strict';
    return Component.extend({
        initialize: function () {
            var self = this;
            self._super();
            customerData.get('devhub-fbeventtracker-subscribe').subscribe(function (loadedData) {
                if (loadedData && "undefined" !== typeof loadedData.events) {
                    for (var eventCounter = 0; eventCounter < loadedData.events.length; eventCounter++) {
                        var eventData = loadedData.events[eventCounter];
                        if ("undefined" !== typeof eventData.eventAdditional && eventData.eventAdditional) {
                            jQuery('.devhub-subscribe-email').text(eventData.eventAdditional.email);
                            jQuery('.devhub-subscribe-id').text(eventData.eventAdditional.id);
                            customerData.set('devhub-fbeventtracker-subscribe', {});
                            return window.fb();
                        }
                    }
                }
            });
        }
    });
});
