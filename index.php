<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SEF Checker</title>
    <link href="http://netdna.bootstrapcdn.com/bootswatch/3.1.1/cerulean/bootstrap.min.css" rel="stylesheet" />  
    <link href="http://netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet" />
    <style>
        textarea{width: 100%;}
    </style>
</head>
<body ng-app="sefChecker">
    <div class="wrapper" ng-controller="SefCheckerController as checker">
    <div class="row"><div class="col-md-12">&nbsp;</div></div>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-5"><textarea rows="20" ng-model="originalList" ng-change="checker.prepareList('originalList')" placeholder="Оригинальные URL'ы"></textarea></div>
        <div class="col-md-5"><textarea rows="20" ng-model="sefList" ng-change="checker.prepareList('sefList')" placeholder="ЧПУ"></textarea></div>
        <div class="col-md-1"></div>
    </div>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <button type="button" class="btn btn-default btn-lg" ng-click="checker.checkUrls()" ng-show="originalList && sefList">
                <span class="glyphicon glyphicon-play-circle"></span> Проверить
            </button>
        </div>
        <div class="col-md-1"></div>
    </div>
    <div class="row" ng-show="originalList && sefList">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <table class="table table-striped">
                <tr ng-repeat="row in checker.urlList">
                    <td>{{row[0]}}</td>
                    <td>{{row[1]}}</td>
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