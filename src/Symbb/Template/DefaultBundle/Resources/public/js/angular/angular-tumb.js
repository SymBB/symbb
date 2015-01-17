(function (angular, factory) {
    if (typeof define === 'function' && define.amd) {
        define('angular-file-upload', ['angular'], function (angular) {
            return factory(angular);
        });
    } else {
        return factory(angular);
    }
}(angular || null, function (angular) {
    var app = angular.module('angularTumb', []);

    /**
     * The ng-thumb directive
     * @author: nerv
     * @version: 0.1.2, 2014-01-09
     */
    app.directive('ngThumb', ['$window', function ($window) {
        var helper = {
            support: !!($window.FileReader && $window.CanvasRenderingContext2D),
            isFile: function (item) {
                return true;//return angular.isObject(item) && item instanceof $window.File;
            },
            isImage: function (file) {
                if (file.type) {
                    var type = '|' + file.type.slice(file.type.lastIndexOf('/') + 1) + '|';
                } else if (file.url) {
                    var type = file.name.split('.');
                    type = type[type.length - 1];
                }
                return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
            }
        };
        return {
            restrict: 'A',
            template: '<canvas/>',
            link: function (scope, element, attributes) {
                if (!helper.support)
                    return;
                var params = scope.$eval(attributes.ngThumb);
                if (!helper.isFile(params.file))
                    return;
                if (!helper.isImage(params.file))
                    return;
                var canvas = element.find('canvas');
                if (params.file.url) {
                    var img = new Image();
                    img.onload = onLoadImage;
                    img.src = params.file.url;
                } else {
                    var reader = new FileReader();
                    reader.onload = onLoadFile;
                    reader.readAsDataURL(params.file);
                }
                function onLoadFile(event) {
                    var img = new Image();
                    img.onload = onLoadImage;
                    img.src = event.target.result;
                }

                function onLoadImage() {
                    var width = params.width || this.width / this.height * params.height;
                    var height = params.height || this.height / this.width * params.width;
                    canvas.attr({width: width, height: height});
                    canvas[0].getContext('2d').drawImage(this, 0, 0, width, height);
                }
            }
        };
    }]);
}));