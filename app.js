(function () {
    var app = angular.module('sefChecker', []);

    var urlLists = [];

    app.controller('SefCheckerController', function($scope) {

        this.urlList = urlLists;

        this.prepareList = function(name) {
            text = $scope[name];
            text = text.replace(/[\n]{2,}/g, "\n");
            text = text.replace(/[\r]{2,}/g, "\r");
            text = text.replace(/[\r\n]{2,}/g, "\r\n");
            text = text.replace(/^\s+/g, "");
            text = text.replace(/\s+$/g, "");
            $scope[name] = text;
        };

        this.checkUrls = function() {
            var original = $scope.originalList.split(/\r|\r\n|\n/);
            var sef = $scope.sefList.split(/\r|\r\n|\n/);
            //if (original.size() == sef.size()) {
                original.forEach(function(item, i) {
                    this.urlList.push([item, sef[i]]);
                });
           // }
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