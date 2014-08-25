symbbControllers.controller('MessageListCtrl', ['$scope', '$http', '$routeParams', '$anchorScroll',
    function($scope, $http, $routeParams, $anchorScroll) {

        $scope.receivedPage = 1;
        $scope.sentPage = 1;

        $scope.getReceivedMessages = function(pagenumber){
            $scope.loadingReceivedMessages = true;
            var route = angularConfig.getSymfonyApiRoute('message_list_received', {'page': pagenumber});
            $http.get(route).success(function(data) {
                $scope.entriesReceived = data.entries;
                $scope.paginationDataRecevied = data.paginationData;
                $scope.loadingReceivedMessages = false;
            });
        };
        $scope.getSentMessages = function(pagenumber){
            $scope.loadingSentMessages = true;
            var route = angularConfig.getSymfonyApiRoute('message_list_sent', {'page': pagenumber});
            $http.get(route).success(function(data) {
                $scope.entriesSent = data.entries;
                $scope.paginationDataSent = data.paginationData;
                $scope.loadingSentMessages = false;
            });
        };

        $scope.paginateReceivedBack = function(){
            var current = $scope.paginateReceivedBack.current;
            var startPage = $scope.paginateReceivedBack.startPage;
            var page = parseInt(current) - 1;
            if(page < startPage){
                page = startPage;
            }
            $scope.getReceivedMessages(page)
        };

        $scope.paginateSentNext = function(){
            var page = $scope.paginationDataSent.next
            $scope.getSentMessages(page)
        };

        $scope.paginateSentBack = function(){
            var current = $scope.paginateSentBack.current;
            var startPage = $scope.paginateSentBack.startPage;
            var page = parseInt(current) - 1;
            if(page < startPage){
                page = startPage;
            }
            $scope.getSentMessages(page)
        };

        $scope.paginateReceivedNext = function(){
            var page = $scope.paginationDataRecevied.next
            $scope.getReceivedMessages(page)
        };

        $scope.getReceivedMessages($scope.receivedPage);
        $scope.getSentMessages($scope.sentPage);

        $anchorScroll();
    }
]).controller('MessageNewCtrl', ['$scope', '$http', '$routeParams' ,'$anchorScroll', '$location', '$timeout',
    function($scope, $http, $routeParams, $anchorScroll, $location, $timeout) {
        $scope.message = {
            id: 0,
            subject: '',
            message: '',
            receivers: []
        }

        $scope.save = function(){
            symbbMessageSave($scope.message, $http, $timeout, $location);
        }

        $anchorScroll();
    }
]).controller('MessageShowCtrl', ['$scope', '$http', '$routeParams', '$anchorScroll', '$location', '$timeout',
    function($scope, $http, $routeParams, $anchorScroll, $location, $timeout) {
        var messageId = 0;
        if ($routeParams && $routeParams.id) {
            messageId = $routeParams.id;
        }
        var route = angularConfig.getSymfonyApiRoute('message_show', {'id': messageId});
        $http.get(route).success(function(data) {
            $.each(data, function(key, value) {
                $scope[key] = value;
            });
            $scope.replyMessage = {
                id: 0,
                subject: '',
                message: '',
                receivers: [$scope.message.sender.id]
            }
        });
        $scope.sendReply = function(){
            symbbMessageSave($scope.replyMessage, $http, $timeout, $location);
        }
        $anchorScroll();
    }
]);


function symbbMessageSave(message, $http, $timeout, $location){
    $http.post(angularConfig.getSymfonyApiRoute('message_save', {}), message).success(function(data) {
        if (data.success) {
            angularConfig.goTo($timeout, $location, 'message_list');
        }
    });
}