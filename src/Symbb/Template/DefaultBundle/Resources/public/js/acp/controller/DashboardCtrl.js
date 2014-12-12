symbbControllers.controller('DashboardCtrl', ['$scope', '$http', '$routeParams', '$anchorScroll', '$route',
    function($scope, $http, $routeParams, $anchorScroll, $route) {
        var pattern = $route.current.$$route.originalPath;
        var routingKey = angularConfig.getRoutingKeyBasedOnPattern(pattern);
        if(routingKey){
            var route = angularConfig.getSymfonyApiRoute(routingKey, $routeParams);
            if(route){
                $http.get(route).success(function(data) {
                    $.each(data, function(key, value) {
                        $scope[key] = value;
                    });

                    if($scope.statistic && $scope.statistic.visitors){

                        var visitorData = $scope.statistic.visitors;
                        var userData =[];
                        var guestData = [];

                        $.each(visitorData.users, function(key, value){
                            var data = [];
                            data[0] = new Date(key).getTime();
                            data[1] = value.length;
                            userData[userData.length] =data;
                        });

                        $.each(visitorData.guests, function(key, value){
                            var data = [];
                            data[0] = new Date(key).getTime();
                            data[1] = value.length;
                            guestData[guestData.length] = data;
                        });

                        var data = [
                            { data: userData, label: "Users" },
                            { data: guestData, label: "Guests"}
                        ];

                        var options = {
                            canvas: true,
                            xaxes: [ { mode: "time" } ],
                            yaxes: [ { min: 0 }, {
                                position: "right",
                                alignTicksWithAxis: 1,
                                tickFormatter: function(value, axis) {
                                    return value.toFixed(axis.tickDecimals) + "€";
                                }
                            } ],
                            legend: { position: "sw" }
                        }

                        $.plot($('#symbb_acp_dashboard_statistic_visitors'), data, options);
                    }
                });
                $anchorScroll();
            } else {
                console.debug('No Api Route found for: '+routingKey)
            }
        } else {
            console.debug('No configured angular route found for: '+pattern)
        }
    }
]);
