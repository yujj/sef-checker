(function () {
    var app = angular.module('sefChecker', []);

    app.controller('SefCheckerController', function($scope, $http) {

        this.urlList = [];
        this.lengthError = false;
        this.httpError = false;

        $scope['originalList'] = 'http://rozbl.ru/catalogue.html?FLOWER=5';
        $scope['sefList'] = 'http://rozbl.ru/catalogue/lepestki-roz.html';

        this.prepareList = function(name) {
            var text = $scope[name];
            text = text.replace(/[\n]{2,}/g, "\n");
            text = text.replace(/[\r]{2,}/g, "\r");
            text = text.replace(/[\r\n]{2,}/g, "\r\n");
            text = text.replace(/^\s*/g, "");
            text = text.replace(/\s*$/g, "");
            $scope[name] = text;
        };

        this.creatList = function() {
            this.lengthError = false;
            if ($scope.originalList != undefined && $scope.sefList != undefined) {
                var original = $scope.originalList.split(/\r|\r\n|\n/);
                var sef = $scope.sefList.split(/\r|\r\n|\n/);
                this.urlList = [];
                if (original.length == sef.length) {
                    for (var i = 0; i < original.length; i++) {
                        this.urlList.push({original: {url: original[i], http_code: 0, status: ''}, sef: {url: sef[i], http_code: 0, status: false}});
                    }
                }else {
                    this.lengthError = true;
                }
            }
        };

        this.checkUrls = function(index) {
            this.creatList();
            this.httpError = false;
            if (index < this.urlList.length) {
                this.urlList[index].original.status = 1;

                var responsePromise = $http.get("response.php?url="+this.urlList[index].original.url);

                responsePromise.success(function(data, status, headers, config) {
                   // this.urlList[index].original.http_code = data.http_code;
                    if (data.http_code == "301") {
                        this.urlList[index].original.status = 2;
                    }
                    this.urlList[index].original.status = 2;
                   
                });

                responsePromise.error(function(data, status, headers, config) {
                    this.httpError = true;
                });
            }
        };

    });

}());
/*
    $(document).ready(function() {
        $("#check").click(function() {
            var original = prepareList($("#original").val());
            $("#original").val(original);
            var sef = prepareList($("#sef").val());
            $("#sef").val(sef);
            var original = original.split(/\r|\r\n|\n/);
            var origCount = original.length;
            var sef = sef.split(/\r|\r\n|\n/);
            var sefCount = sef.length;
            var result = $("#result");
            result.html("");
            if (origCount == sefCount) {
                for (var i=0; i < origCount; i++) {
                    result.append("<tr><td class='number'>"+(i+1)+"</td><td class='url' id='orig"+i+"'><a rel='"+i+"' class='original' href='"+original[i]+"'>"+original[i]+"</a><span class='status'>wating</span></td><td class='url' id='sef"+i+"'><a href='"+sef[i]+"'>"+sef[i]+"</a><span class='status'>wating</span></td><td class='update'><img title='Обновить' onclick='checkRow("+i+", false)' src='update.png'></td></tr>");
                }
            }else {
                alert("Количество адресов в полях должно совпадать");
                return;
            }
            checkRow(0, true);
        })
    });

    function checkRow(i, chekAll) {
        var obj = $("a[rel="+i+"]");
        if (obj == undefined) {
            return;
        }
        $("td#orig"+i+" .status").toggleClass("loading");
        var origRes = null;
        var sefRes = null;
        $.ajax({
            type: 'POST',
            url: 'index.php',
            data: {url:obj.attr("href")},
            dataTypr: "json",
            success: function(res) {
                origRes = res;
            },
            complete: function() {
                $("td#orig"+i+" .status, td#sef"+i+" .status").toggleClass("loading");
                $.ajax({
                    type: 'POST',
                    url: 'index.php',
                    data: {url:$("td#sef"+i+" a").attr("href")},
                    success: function(res) {
                        sefRes = res;
                    },
                    complete: function() {
                        $("td#sef"+i+" .status").toggleClass("loading");
                        if (origRes == null || sefRes == null) {
                            $("td#orig"+i+" .status, td#sef"+i+" .status").attr("class", "status").addClass("connectionError error").html("");
                        }else {
                            if (origRes["http_code"] != 301) {
                                if (origRes["http_code"] == null) {
                                    origClass = "notAnswer error";
                                }else {
                                    origClass = "error";
                                }
                            }else if (origRes["url"] != sefRes["orig_url"]) {
                                origClass = "wrongRedirect error";
                            }else if (origRes["redirect_count"] > 1) {
                                origClass = "multiRedirect warning";
                                origRes["http_code"] = "<a target='_blank' title='Проверить через bertal' href='http://www.bertal.ru/index.php?url="+origRes["orig_url"]+"'>"+origRes["http_code"]+"</a>";
                            }else {
                                origClass = "ok";
                            }
                            if (sefRes["http_code"] == 200) {
                                sefClass = "ok";
                            }else {
                                if (sefRes["http_code"] == null) {
                                    sefClass = "notAnswer error";
                                }else {
                                    sefClass = "error";
                                }
                            }
                            $("td#orig"+i+" .status").attr("class", "status").addClass(origClass).html(origRes["http_code"]);
                            $("td#sef"+i+" .status").attr("class", "status").addClass(sefClass).html(sefRes["http_code"]);
                            if (chekAll) {
                                checkRow(i+1, true);
                            }
                        }
                    },
                    error: function(res) {
                        //alert("Ошибка");
                    }
                });
            },
            error: function(res) {
                //alert("Ошибка");
            }
        });
    }
*/