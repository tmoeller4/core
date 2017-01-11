/**
 * @namespace biigle.ui.messages
 * @ngdoc service
 * @name msg
 * @memberOf biigle.ui.messages
 * @description Enables arbitrary AngularJS modules to post user feedback messages using the UI messaging system. See the [Bootstrap alerts](http://getbootstrap.com/components/#alerts) for available message types and their style. In addition to actively posting messages, it provides the `responseError` method to conveniently display error messages in case an AJAX request went wrong.
 * @example
msg.post('danger', 'Do you really want to delete this? Everything will be lost.');

msg.danger('Do you really want to delete this? Everything will be lost.');
msg.warning('Leaving the project is not reversible.');
msg.success('The project was created.');
msg.info('You will receive an email about this.');

var label = AnnotationLabel.attach({ ... });
// handles all error responses automatically
label.$promise.catch(msg.responseError);
 */
angular.module('biigle.ui.messages').service('msg', function () {
        "use strict";
        var _this = this;

        this.post = function (type, message) {
            message = message || type;
            window.$biiglePostMessage(type, message);
        };

        this.danger = function (message) {
            _this.post('danger', message);
        };

        this.warning = function (message) {
            _this.post('warning', message);
        };

        this.success = function (message) {
            _this.post('success', message);
        };

        this.info = function (message) {
            _this.post('info', message);
        };

        this.responseError = function (response) {
            var data = response.data;

            if (data) {
                if (data.message) {
                    // error response
                    _this.danger(data.message);
                } else if (typeof data === 'string') {
                    // unknown error response
                    _this.danger(data);
                } else {
                    // validation response
                    for (var key in data) {
                        _this.danger(data[key][0]);
                    }
                }
            } else if (response.status === 403) {
                _this.danger("You have no permission to do that.");
            } else if (response.status === 401) {
                _this.danger("Please log in (again).");
            } else {
                _this.danger("The server didn't respond, sorry.");
            }
        };
    }
);
