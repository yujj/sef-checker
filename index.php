<!DOCTYPE html>
<html xmlns:ng="http://angularjs.org" ng-app="sefChecker">
<head>
    <meta charset="utf-8">
    <title>SEF Checker</title>
    <link href="http://netdna.bootstrapcdn.com/bootswatch/3.1.1/cerulean/bootstrap.min.css" rel="stylesheet" />  
    <link href="http://netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet" />
    <style>
        textarea{width: 100%;}
        .glyphicon{top: 3px;}
        .refresh{width: 40px;color: blue;}
        .row{margin-bottom: 1em;}
        .label a{color: #fff;text-decoration: none;}
    </style>
</head>
<body ng-controller="SefCheckerController">
    <div class="wrapper">
    <div class="row"><div class="col-md-12">&nbsp;</div></div>    
    <div class="row" ng-show="lengthError || httpError" ng-cloak>
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <div class="alert alert-danger" ng-show="lengthError">Количество ссылок в полях должно быть одинаково</div>&nbsp;
            <div class="alert alert-danger" ng-show="httpError">Ошибка при HTTP запросе</div>
        </div>
        <div class="col-md-1"></div>
    </div>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-5"><textarea rows="20" ng-model="originalList" ng-change="prepareList('originalList');creatList()" placeholder="Оригинальные URL'ы"></textarea></div>
        <div class="col-md-5"><textarea rows="20" ng-model="sefList" ng-change="prepareList('sefList');creatList()" placeholder="ЧПУ"></textarea></div>
        <div class="col-md-1"></div>
    </div>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <button type="button" class="btn btn-default btn-lg" ng-click="checkUrls(0)" ng-show="originalList && sefList && !lengthError">
                <span class="glyphicon glyphicon-play-circle"></span> Проверить
            </button>
        </div>
        <div class="col-md-1"></div>
    </div>
    <div class="row" ng-show="originalList && sefList" ng-cloak>
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <table class="table table-striped">
                <tr ng-repeat="(index, row) in urlList">
                    <td>
                        <span ng-show="row.original.status == 1" class="label label-default">whait</span> 
                        <span ng-show="row.original.status == 2" class="label label-success">301</span>
                        <span ng-show="row.original.status == 3" class="label label-warning">301</span>
                        <span ng-show="row.original.status == 4" class="label label-danger">{{row.original.http_code}}</span>
                        <span ng-show="row.original.status > 2" class="label label-primary"><a href="http://www.bertal.ru/index.php?url={{row.original.url}}#h" target="_block">Bertal</a></span>
                        <a href="{{row.original.url}}" target="_block">{{row.original.url}}</a>
                    </td>
                    <td>
                        <span ng-show="row.sef.status == 1" class="label label-default">whait</span> 
                        <span ng-show="row.sef.status == 2" class="label label-success">200</span>
                        <span ng-show="row.sef.status == 3" class="label label-danger">{{row.sef.http_code}}</span>
                        <span ng-show="row.original.status > 2" class="label label-primary"><a href="http://www.bertal.ru/index.php?url={{row.sef.url}}#h" target="_block">Bertal</a></span>
                        <a href="{{row.sef.url}}" target="_block">{{row.sef.url}}</a>
                    </td>
                    <td class="refresh">
                        <div class="btn-group btn-group-xs">
                            <button type="button" class="btn btn-default btn-lg" ng-click="checkOriginalUrl(index, true)">
                                <span class="glyphicon glyphicon-refresh"></span>
                            </button>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-1"></div>
    </div>
    </div>
    <script src="https://code.angularjs.org/1.2.9/angular.min.js"></script>
    <script src="app.js"></script>
</body>
</html>