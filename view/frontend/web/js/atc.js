
define([
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (ko, Component, customerData) {
    'use strict';
    return Component.extend({
        initialize: function () {
            var self = this;
            self._super();
            customerData.get('devhub-fbeventtracker-atc').subscribe(function (loadedData) {
                if (loadedData && "undefined" !== typeof loadedData.events) {
                    for (var eventCounter = 0; eventCounter < loadedData.events.length; eventCounter++) {
                        var eventData = loadedData.events[eventCounter];
                        if ("undefined" !== typeof eventData.eventAdditional && eventData.eventAdditional) {
                            fbq('track', eventData.eventName, eventData.eventAdditional || {});
                        }
                    }
                    customerData.set('devhub-fbeventtracker-atc', {});
                }
            });
        }
    });
});
