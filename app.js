(function () {
    angular.module("sefChecker", [])
    .controller("SefCheckerController", function($scope, $http) {

        $scope.urlList = [];
        $scope.lengthError = false;
        $scope.httpError = false;

        $scope.prepareList = function(name) {
            var text = $scope[name];
            text = text.replace(/[\n]{2,}/g, "\n");
            text = text.replace(/[\r]{2,}/g, "\r");
            text = text.replace(/[\r\n]{2,}/g, "\r\n");
            text = text.replace(/^\s*/g, "");
            text = text.replace(/\s*$/g, "");
            $scope[name] = text;
        };

        $scope.creatList = function() {
            $scope.urlList = [];
            $scope.lengthError = false;
            if ($scope.originalList != undefined && $scope.sefList != undefined) {
                var original = $scope.originalList.split(/\r|\r\n|\n/);
                var sef = $scope.sefList.split(/\r|\r\n|\n/);
                if (original.length == sef.length) {
                    for (var i = 0; i < original.length; i++) {
                        $scope.urlList.push({original: {url: original[i], http_code: 0, status: ''}, sef: {url: sef[i], http_code: 0, status: false}});
                    }
                }else {
                    $scope.lengthError = true;
                }
            }
        };

        $scope.checkUrls = function(index) {
            $scope.creatList();
            $scope.checkOriginalUrl(index, false);
        }

        $scope.checkOriginalUrl = function(index, onse) {
            if ($scope.urlList[index].original.url == '') {
                return false;
            }
            $scope.httpError = false;
            if (index < $scope.urlList.length) {
                $scope.urlList[index].original.status = 1;
                var responsePromise = $http.get("response.php?url="+$scope.urlList[index].original.url);
                responsePromise.success(function(data, status, headers, config) {
                    if (data.http_code == null) {
                        data.http_code = 'Нет ответа';
                    }
                    $scope.urlList[index].original.http_code = data.http_code;
                    if (data.http_code == "301") {
                        if (data.url == $scope.urlList[index].sef.url) {
                            $scope.urlList[index].original.status = 2;
                        }else {
                            $scope.urlList[index].original.status = 3;
                        }                        
                    }else {
                        $scope.urlList[index].original.status = 4;
                    }
                    $scope.checkSeflUrl(index, onse);
                });
                responsePromise.error(function(data, status, headers, config) {
                    $scope.httpError = true;
                });
            }
        }

        $scope.checkSeflUrl = function(index, onse) {
            if ($scope.urlList[index].sef.url == '') {
                return false;
            }
            $scope.httpError = false;
            if (index < $scope.urlList.length) {
                $scope.urlList[index].sef.status = 1;
                var responsePromise = $http.get("response.php?url="+$scope.urlList[index].sef.url);
                responsePromise.success(function(data, status, headers, config) {
                    if (data.http_code == null) {
                        data.http_code = 'Нет ответа';
                    }
                    $scope.urlList[index].sef.http_code = data.http_code;
                    if (data.http_code == "200") {
                        $scope.urlList[index].sef.status = 2;                        
                    }else {
                        $scope.urlList[index].sef.status = 3;
                    }
                    if (!onse) {
                        $scope.checkOriginalUrl(index+1, false);
                    }
                });
                responsePromise.error(function(data, status, headers, config) {
                    $scope.httpError = true;
                });
            }
        }

    });

}());