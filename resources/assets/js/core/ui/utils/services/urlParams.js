/**
 * @namespace dias.ui.utils
 * @ngdoc service
 * @name urlParams
 * @memberOf dias.ui.utils
 * @description Manages URL parameters
 */
angular.module('dias.ui.utils').service('urlParams', function ($location) {
        "use strict";

        this.setSlug = function (s) {
            // get path without slug
            var path = $location.path();
            path = path.substring(0, path.lastIndexOf('/'));

            $location.path(path + '/' + s);
            $location.replace();
        };

        // sets a URL parameter and updates the history state
        this.set = function (params) {
            $location.search(params);
            $location.replace();
        };

        this.unset = function (key) {
            $location.search(key, null);
        };

        // returns a URL parameter
        this.get = function (key) {
            return $location.search()[key];
        };
    }
);