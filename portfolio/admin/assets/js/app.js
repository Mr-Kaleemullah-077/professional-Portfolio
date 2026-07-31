/* ============================================================
   ADMIN APP - compiled JavaScript
   Generated from admin/assets/ts/app.ts (TypeScript).
   Loads dashboard stats from the PHP JSON API.
   ============================================================ */

var AdminApp;
(function (AdminApp) {
    var DashboardController = (function () {
        DashboardController.$inject = ['$scope', '$http'];
        function DashboardController($scope, $http) {
            this.$scope = $scope;
            this.$http = $http;
            this.stats = {
                counts: { projects: 0, services: 0, skills: 0, messages: 0, unread: 0 },
                projects_by_category: [],
                skills: [],
                recent_messages: []
            };
            this.loadStats();
            this.$scope.stats = this.stats;
            this.$scope.chartWidth = (function (_this) {
                return function (item) {
                    var total = 0;
                    _this.stats.projects_by_category.forEach(function (c) { return (total += c.count); });
                    return total > 0 ? Math.round((item.count / total) * 100) + '%' : '0%';
                };
            })(this);
        }
        DashboardController.prototype.loadStats = function () {
            var _this = this;
            this.$http.get('../api/stats.php')
                .then(function (res) {
                    _this.stats = res.data;
                    _this.$scope.stats = _this.stats;
                })
                .catch(function () {
                    console.error('Failed to load dashboard stats.');
                });
        };
        return DashboardController;
    })();

    var app = angular.module('adminApp', []);
    app.controller('DashboardCtrl', DashboardController);
})(AdminApp || (AdminApp = {}));
