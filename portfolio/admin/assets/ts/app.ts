/* ============================================================
   ADMIN APP - TypeScript source
   Angular.js controller that loads dashboard stats from the
   PHP JSON API (../api/stats.php)

   Compile with:  tsc admin/assets/ts/app.ts --outFile admin/assets/js/app.js
   The compiled output is also committed so the site works
   without a build step.
   ============================================================ */

declare var angular: any;

namespace AdminApp {

  export interface Counts {
    projects: number;
    services: number;
    skills: number;
    messages: number;
    unread: number;
  }

  export interface CategoryCount {
    category: string;
    count: number;
  }

  export interface Skill {
    name: string;
    percent: number;
    category: string;
  }

  export interface MessageRow {
    id: number;
    name: string;
    email: string;
    subject: string;
    is_read: number;
    created_at: string;
  }

  export interface StatsData {
    counts: Counts;
    projects_by_category: CategoryCount[];
    skills: Skill[];
    recent_messages: MessageRow[];
  }

  export interface DashboardScope extends angular.IScope {
    stats: StatsData;
    chartWidth: (item: CategoryCount) => string;
  }

  export class DashboardController {
    public stats: StatsData = {
      counts: { projects: 0, services: 0, skills: 0, messages: 0, unread: 0 },
      projects_by_category: [],
      skills: [],
      recent_messages: []
    };

    public static $inject: string[] = ['$scope', '$http'];

    constructor(private $scope: DashboardScope, private $http: any) {
      this.loadStats();
      this.$scope.stats = this.stats;
      this.$scope.chartWidth = (item: CategoryCount): string => {
        const total = this.totalProjects(item);
        return total > 0 ? Math.round((item.count / total) * 100) + '%' : '0%';
      };
    }

    private totalProjects(item: CategoryCount): number {
      let total = 0;
      this.stats.projects_by_category.forEach((c: CategoryCount) => (total += c.count));
      return total;
    }

    private loadStats(): void {
      this.$http.get('../api/stats.php')
        .then((res: any) => {
          this.stats = res.data;
          this.$scope.stats = this.stats;
        })
        .catch(() => {
          console.error('Failed to load dashboard stats.');
        });
    }
  }

  const app = angular.module('adminApp', []);
  app.controller('DashboardCtrl', DashboardController);
}
