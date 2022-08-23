<?php

namespace App\Services;

use Illuminate\Support\Facades\Request;
use Carbon\Carbon;
use App\Entity\RoleUser;

class FilterService
{
    public $fields = [];

    public function __construct()
    {
        $this->fields = [
            'issues.id as id',  // id
            'drawing_plans.block as block', // block
            'drawing_plans.level as level', // level
            'drawing_plans.unit as unit', // unit
            'issues.reference as reference', // reference
            'issues.position_x as position_x', // pos_X
            'issues.position_y as position_y', // pos_y
            'locations.id as location_id', // location
            'locations.name as location',
            'setting_category.id as category_id', // type
            'setting_category.name as category',
            'setting_types.id as type_id', // subtype
            'setting_types.name as type',
            'setting_issues.id as issue_id', // description
            'setting_issues.name as issue',
            'setting_priority.id as priority_id', // priority
            'setting_priority.name as priority',
            'setting_priority.type as priority_type',
            'setting_priority.no_of_days as priority_nb_day',
            'status.id as status_id', // status
            'status.internal as status',
            'issues.deleted_at as deleted_date', // archived
            \DB::raw('DATE_FORMAT(issues.created_at, "%d/%m/%Y") as creation_date'),
            'issues.created_by as created_by_id', // createdby
            'creators.name as created_by',
            \DB::raw('DATE_FORMAT(issues.confirmed_date, "%d/%m/%Y") as confirmation_date'),
            'issues.confirmed_by as confirmed_by_id', // confirmed_by
            'confirmors.name as confirmed_by',
            \DB::raw('DATE_FORMAT(issues.due_by, "%d/%m/%Y") as target_completion_date'),
            'issues.group_id as contractor_id', // contrator
            'contrators.display_name as contractor',
            \DB::raw('DATE_FORMAT(issues.start_date, "%d/%m/%Y") as work_start_date'),
            'issues.completed_by as completed_by_id', // completed_by
            'completors.display_name as completed_by',
            \DB::raw('DATE_FORMAT(issues.completed_date, "%d/%m/%Y") as completion_date'),
            'issues.closed_by as closed_by_id', // closed_by
            'closers.name as closed_by',
            \DB::raw('DATE_FORMAT(issues.closed_date, "%d/%m/%Y") as closing_date'),
            'issues.remarks as remarks',  // remarks
            'drawing_plans.id as drawing_plan_id',
            'drawing_plans.file as drawing_plan_file',
            'drawing_plans.height as drawing_plan_file_height',
            'drawing_plans.width as drawing_plan_file_width',
            'clients.logo as client_logo',
            'clients.name as client_name',
            'projects.name as project_name',
            'before_images.image as before_image_file',
            'before_images.created_at as before_image_time',
            'after_images.image as after_image_file',
            'after_images.created_at as after_image_time'
        ];
    }

    public function generateProjectSummaryQuery()
    {
        $field = [
            'projects.id as project_id',
            'projects.name as project_name',
            \DB::raw('count(drawing_plans.id) as units_count')
        ];

        $count = \DB::table('projects')
            ->join('drawing_sets', function($query) {
                $query->on('drawing_sets.project_id', '=', 'projects.id')
                    ->whereNull('drawing_sets.deleted_at');
            })
            ->join('drawing_plans', function($query) {
                $query->on('drawing_plans.drawing_set_id', '=', 'drawing_sets.id')
                    ->whereIn('drawing_plans.types', ['unit'])
                    ->whereNull('drawing_plans.deleted_at');
            })
            ->join('clients', 'projects.client_id', 'clients.id')
            ->whereNull('projects.deleted_at')
            ->select($field)
            ->groupBy('projects.id')
            ->orderBy('projects.name');

        return $count;
    }

    public function generateUnitSummaryQuery($filter = [])
    {
        $today = Carbon::today();
        $day_6_before_today = Carbon::today()->subDays(6)->addDay();
        $day_7_before_today = Carbon::today()->subDays(7)->addDay();
        $day_14_before_today = Carbon::today()->subDays(14)->addDay();
        $day_15_before_today = Carbon::today()->subDays(15)->addDay();
        $day_22_before_today = Carbon::today()->subDays(22)->addDay();
        $day_23_before_today = Carbon::today()->subDays(23)->addDay();
        $day_30_before_today = Carbon::today()->subDays(30)->addDay();

        $query = '(status.internal = "W.I.P" OR status.internal = "Redo") AND issues.start_date <> "0000-00-00"';

        $field = [
            'drawing_plans.id as id',
            'drawing_plans.block as block',
            'drawing_plans.level as level',
            'drawing_plans.unit as unit',
            \DB::raw('sum(status.internal = "Lodged" OR status.internal = "New") new_issues'),
            \DB::raw('sum(' . $query .') wip_issues'),
            \DB::raw('sum(status.internal = "Completed") completed_issues'),
            \DB::raw('sum(status.internal = "Closed") closed_issues'),
            \DB::raw('sum(' . $query . ' AND issues.start_date <="' . $today->format('Y-m-d') . '" AND issues.start_date >="' . $day_6_before_today->format('Y-m-d') . '") issues_less_than_7_days'),
            \DB::raw('sum(' . $query . ' AND issues.start_date <="' . $day_7_before_today->format('Y-m-d') . '" AND issues.start_date >="' . $day_14_before_today->format('Y-m-d') . '") issues_7_to_14_days'),
            \DB::raw('sum(' . $query . ' AND issues.start_date <="' . $day_15_before_today->format('Y-m-d') . '" AND issues.start_date >="' . $day_22_before_today->format('Y-m-d') . '") issues_15_to_22_days'),
            \DB::raw('sum(' . $query . ' AND issues.start_date <="' . $day_23_before_today->format('Y-m-d') . '" AND issues.start_date >="' . $day_30_before_today->format('Y-m-d') . '") issues_23_to_30_days'),
            \DB::raw('sum(' . $query . ' AND issues.start_date <"' . $day_30_before_today->format('Y-m-d') . '") issues_more_than_30_days'),
        ];

        $issues = \DB::table('drawing_plans')
            ->select($field)
            ->join('drawing_sets', 'drawing_sets.id', 'drawing_plans.drawing_set_id')
            ->join('projects', 'projects.id', 'drawing_sets.project_id')
            ->leftJoin('locations', 'locations.drawing_plan_id', 'drawing_plans.id')
            ->leftJoin('issues', 'issues.location_id', 'locations.id')
            ->leftJoin('status', 'status.id', 'issues.status_id')
            ->groupBy('drawing_plans.id');

        if ($filter) {
            if (isset($filter['project_id']) && !empty($filter['project_id'])) {
                $issues->where('drawing_sets.project_id', $filter['project_id']);
            }

            if (isset($filter['type']) && $filter['type']) {
                $issues = $issues->where('drawing_plans.types', $filter['type']);
            }

            if (isset($filter['block']) && $filter['block']) {
                $issues = $issues->where('drawing_plans.block', $filter['block']);
            }

            if (isset($filter['level']) && $filter['level']) {
                $issues = $issues->where('drawing_plans.level', $filter['level']);
            }

            if (isset($filter['unit']) && $filter['unit']) {
                $issues = $issues->where('drawing_plans.unit', $filter['unit']);
            }

            if (isset($filter['active']) && $filter['active'] && $filter['active'] != 'all') {
                $issues = $issues->having($filter['active'], '>', 0);
            }
        }

        return $issues;
    }

    public function generateIssueQuery($filter = [])
    {
        $issues = \DB::table('issues')
            ->join('setting_issues', 'setting_issues.id', '=', 'issues.setting_issue_id') // for setting
            ->join('setting_types', 'setting_types.id', '=', 'setting_issues.type_id')
            ->join('setting_category', 'setting_category.id', '=', 'setting_types.category_id')
            ->join('status', 'status.id', '=', 'issues.status_id') // for status
            ->leftJoin('setting_priority', 'setting_priority.id', '=', 'issues.priority_id') // for priority
            ->join('locations', 'locations.id', '=', 'issues.location_id') //locations -> drawing_plan -> drawing_set -> get project id
            ->join('drawing_plans', 'drawing_plans.id', '=', 'locations.drawing_plan_id')
            ->join('drawing_sets', 'drawing_sets.id', '=', 'drawing_plans.drawing_set_id')
            ->join('projects', 'drawing_sets.project_id', 'projects.id') // project
            ->join('clients', 'projects.client_id', 'clients.id') // client
            ->leftJoin('users as creators', 'creators.id', '=', 'issues.created_by') // creators
            ->leftJoin('users as confirmors', 'confirmors.id', '=', 'issues.confirmed_by') // confirmors
            ->leftJoin('groups as contrators', 'contrators.id', '=', 'issues.group_id') // contractors
            ->leftJoin('groups as completors', 'completors.id', '=', 'issues.completed_by') // completors - whoever complete the job
            ->leftJoin('users as closers', 'closers.id', '=', 'issues.closed_by') // closers
            ->leftJoin('issue_images as before_images', function ($join) {
                $join->on('before_images.issue_id', '=', 'issues.id')
                    ->where('before_images.type', '=', 1)
                    ->where('before_images.seq', '=', 1)
                    ->whereNull('before_images.deleted_at');
            }) // before image
            ->leftJoin('issue_images as after_images', function ($join) {
                $join->on('after_images.issue_id', '=', 'issues.id')
                    ->where('after_images.type', '=', 2)
                    ->where('after_images.seq', '=', 1)
                    ->whereNull('after_images.deleted_at');
            })
            ->whereNull('issues.deleted_at'); // after image

        if ($filter) {
            if (isset($filter['project_id']) && !empty($filter['project_id'])) {
                $issues->where('drawing_sets.project_id', $filter['project_id']);
            }
    
            if (isset($filter['status']) && !empty($filter['status'])) {
                $issues->where('issues.status_id', $filter['status']);
            }

            if (isset($filter['external']) && !empty($filter['external'])) {
                $issues->where('status.external', $filter['external']);
            }
            
            if (isset($filter['priority']) && !empty($filter['priority'])) {
                $issues->where('issues.priority_id', $filter['priority']);
            }
    
            if (isset($filter['unit_type']) && !empty($filter['unit_type'])) {
                $issues->where('drawing_plans.types', $filter['unit_type']);
            }
    
            if (isset($filter['category']) && !empty($filter['category'])) {
                $issues->where('setting_category.id', $filter['category']);
            }
    
            if (isset($filter['location']) && !empty($filter['location'])) {
                $issues->where('locations.id', $filter['location']);
            }
    
            if (isset($filter['block']) && !empty($filter['block'])) {
                $issues->where('drawing_plans.block', $filter['block']);
            }
    
            if (isset($filter['level']) && !empty($filter['level'])) {
                $issues->where('drawing_plans.level', $filter['level']);
            }
    
            if (isset($filter['unit']) && !empty($filter['unit'])) {
                $issues->where('drawing_plans.unit', $filter['unit']);
            }

            if (isset($filter['contractor']) && !empty($filter['contractor'])) {
                $issues->where('issues.group_id', $filter['contractor']);
            }
    
            if (isset($filter['date_type']) && !empty($filter['date_type'])) {
                $date_field = 'issues.created_at';
                switch ($filter['date_type']) {
                    case 1:
                        $date_field = 'issues.created_at';
                        break;
                    case 3:
                        $date_field = 'issues.due_by';
                        break;
                    case 4:
                        $date_field = 'issues.completed_date';
                        break;
                    case 5:
                        $date_field = 'issues.closed_date';
                        break;
                    default:
                        $date_field = 'issues.created_at';
                        break;
                }
    
                if (isset($filter['from']) && !empty($filter['from'])) {
                    $issues->whereDate($date_field, '>=', $filter['from']);
                }
    
                if (isset($filter['to']) && !empty($filter['to'])) {
                    $issues->whereDate($date_field, '<=', $filter['to']);
                }
            }
        }
        
        return $issues;
    }

    public function generateContractorSummaryQuery($filter = [])
    {
        $today = Carbon::today()->format('Y-m-d');
        $day_6_before_today = Carbon::today()->subDays(6)->addDay()->format('Y-m-d');
        $day_7_before_today = Carbon::today()->subDays(7)->addDay()->format('Y-m-d');
        $day_14_before_today = Carbon::today()->subDays(14)->addDay()->format('Y-m-d');
        $day_15_before_today = Carbon::today()->subDays(15)->addDay()->format('Y-m-d');
        $day_22_before_today = Carbon::today()->subDays(22)->addDay()->format('Y-m-d');
        $day_23_before_today = Carbon::today()->subDays(23)->addDay()->format('Y-m-d');
        $day_30_before_today = Carbon::today()->subDays(30)->addDay()->format('Y-m-d');

        $ext_query = "issues.deleted_at is NULL";
        if (isset($filter['project_id']) && $filter['project_id']) {
            $ext_query .= " AND drawing_sets.project_id = '{$filter['project_id']}'";

            if (isset($filter['block']) && $filter['block']) {
                $ext_query .= " AND drawing_plans.block = '{$filter['block']}'";
            }

            if (isset($filter['level']) && $filter['level']) {
                $ext_query .= " AND drawing_plans.level = '{$filter['level']}'";
            }

            if (isset($filter['unit']) && $filter['unit']) {
                $ext_query .= " AND drawing_plans.unit = '{$filter['unit']}'";
            }

        }

        $field = [
            'groups.id as id',
            'groups.display_name as contractor_name',
            'groups.abbreviation_name as contractor_abb_name',
            \DB::raw('COALESCE(issues.total, 0) as issues_total'),
            \DB::raw('COALESCE(issues.new_issues, 0) as new_issues'),
            \DB::raw('COALESCE(issues.pending_start_issues, 0) as pending_start_issues'),
            \DB::raw('COALESCE(issues.void_issues, 0) as void_issues'),
            \DB::raw('COALESCE(issues.wip_issues, 0) as wip_issues'),
            \DB::raw('COALESCE(issues.overdue_issues, 0) as overdue_issues'),
            \DB::raw('COALESCE(issues.not_me_issues, 0) as not_me_issues'),
            \DB::raw('COALESCE(issues.reassign_issues, 0) as reassign_issues'),
            \DB::raw('COALESCE(issues.completed_issues, 0) as completed_issues'),
            \DB::raw('COALESCE(issues.redo_issues, 0) as redo_issues'),
            \DB::raw('COALESCE(issues.closed_issues, 0) as closed_issues'),
            \DB::raw('COALESCE(issues.issues_less_than_7_days, 0) as issues_less_than_7_days'),
            \DB::raw('COALESCE(issues.issues_7_to_14_days, 0) as issues_7_to_14_days'),
            \DB::raw('COALESCE(issues.issues_15_to_22_days, 0) as issues_15_to_22_days'),
            \DB::raw('COALESCE(issues.issues_23_to_30_days, 0) as issues_23_to_30_days'),
            \DB::raw('COALESCE(issues.issues_more_than_30_days, 0) as issues_more_than_30_days'),
        ];

        $issues = \DB::table('issues')
            ->join('locations', 'locations.id', '=', 'issues.location_id')
            ->join('drawing_plans', 'drawing_plans.id', '=', 'locations.drawing_plan_id')
            ->join('drawing_sets', 'drawing_sets.id', '=', 'drawing_plans.drawing_set_id')
            ->select([
                "issues.group_id", 
                \DB::raw("count(CASE WHEN $ext_query THEN 1 END) as total"),
                \DB::raw("count(CASE WHEN ($ext_query AND issues.status_id = 2) THEN 1 END) as new_issues"),
                \DB::raw("count(CASE WHEN ($ext_query AND issues.status_id = 3) THEN 1 END) as pending_start_issues"),
                \DB::raw("count(CASE WHEN ($ext_query AND issues.status_id = 4) THEN 1 END) as void_issues"),
                \DB::raw("count(CASE WHEN ($ext_query AND issues.status_id = 5) THEN 1 END) as wip_issues"),
                \DB::raw("count(CASE WHEN ($ext_query AND issues.status_id = 5 AND issues.due_by < '$today') THEN 1 END) as overdue_issues"),
                \DB::raw("count(CASE WHEN ($ext_query AND issues.status_id = 6) THEN 1 END) as not_me_issues"),
                \DB::raw("count(CASE WHEN ($ext_query AND issues.status_id = 7) THEN 1 END) as reassign_issues"),
                \DB::raw("count(CASE WHEN ($ext_query AND issues.status_id = 8) THEN 1 END) as completed_issues"),
                \DB::raw("count(CASE WHEN ($ext_query AND issues.status_id = 9) THEN 1 END) as redo_issues"),
                \DB::raw("count(CASE WHEN ($ext_query AND issues.status_id = 10) THEN 1 END) as closed_issues"),
                \DB::raw("count(CASE WHEN ($ext_query AND issues.status_id = 5 AND issues.start_date <= '$today' AND issues.start_date >= '$day_6_before_today') THEN 1 END) issues_less_than_7_days"),
                \DB::raw("count(CASE WHEN ($ext_query AND issues.status_id = 5 AND (issues.start_date <= '$day_7_before_today' AND issues.start_date >= '$day_14_before_today')) THEN 1 END) issues_7_to_14_days"),
                \DB::raw("count(CASE WHEN ($ext_query AND issues.status_id = 5 AND (issues.start_date <= '$day_15_before_today' AND issues.start_date >= '$day_22_before_today')) THEN 1 END) issues_15_to_22_days"),
                \DB::raw("count(CASE WHEN ($ext_query AND issues.status_id = 5 AND (issues.start_date <= '$day_23_before_today' AND issues.start_date >= '$day_30_before_today')) THEN 1 END) issues_23_to_30_days"),
                \DB::raw("count(CASE WHEN ($ext_query AND issues.status_id = 5 AND issues.start_date < '$day_30_before_today') THEN 1 END) issues_more_than_30_days"),
            ])
            ->groupBy('issues.group_id');

        $contractor = \DB::table('group_project')
            ->join('groups', 'groups.id', 'group_project.group_id')
            ->leftJoin(\DB::raw('(' . $issues->toSql() . ') issues'), function($join) {
                $join->on('issues.group_id', '=', 'groups.id');
            })
            ->groupBy('groups.id')
            ->whereNull('group_project.deleted_at')
            ->select($field);

        if (isset($filter['active']) && $filter['active'] && $filter['active'] != 'all') {
            $contractor = $contractor->having($filter['active'], '>', 0);
        }

        if (isset($filter['project_id']) && $filter['project_id']) {
            $contractor = $contractor->where('group_project.project_id', $filter['project_id']);
        }

        return $contractor;
    }

    public function generateTypeSummaryQuery($filter = []) 
    {
        $extra_query = '';
        if ($filter) {
            if (isset($filter['project_id']) && $filter['project_id']) {
                $extra_query = ' AND drawing_sets.project_id="' . $filter['project_id'] . '"';
            }
        }

        $field = [
            'setting_issues.id as issue_id',
            'setting_issues.name as issue_name',
            'setting_types.name as type_name',
            'setting_category.name as category_name',
            \DB::raw('COALESCE(sum(issues.id != "null"' . $extra_query . '), 0) sum'),
            \DB::raw('COALESCE(sum((issues.status_id = "1")' . $extra_query . '), 0) lodged_issues'),
            \DB::raw('COALESCE(sum((issues.status_id = "2")' . $extra_query . '), 0) new_issues'),
            \DB::raw('COALESCE(sum((issues.status_id = "3")' . $extra_query . ' ), 0) pending_start_issues'),
            \DB::raw('COALESCE(sum((issues.status_id = "4")' . $extra_query . ' ), 0) rejected_issues'),
            \DB::raw('COALESCE(sum((issues.status_id = "5") ' . $extra_query . ' ), 0) wip_issues'),
            \DB::raw('COALESCE(sum((issues.status_id = "6") ' . $extra_query . ' ), 0) not_me_issues'),
            \DB::raw('COALESCE(sum((issues.status_id = "7") ' . $extra_query . ' ), 0) reassign_issues'),
            \DB::raw('COALESCE(sum((issues.status_id = "8") ' . $extra_query . ' ), 0) completed_issues'),
            \DB::raw('COALESCE(sum((issues.status_id = "9") ' . $extra_query . ' ), 0) redo_issues'),
            \DB::raw('COALESCE(sum((issues.status_id = "10") ' . $extra_query . ' ), 0) closed_issues'),
        ];

        $setting_issues = \DB::table('setting_issues')
            ->join('setting_types', 'setting_types.id', '=', 'setting_issues.type_id')
            ->join('setting_category', 'setting_category.id', '=', 'setting_types.category_id')
            ->leftJoin('issues', 'setting_issues.id', '=', 'issues.setting_issue_id')
            ->leftJoin('locations', 'locations.id', '=', 'issues.location_id') //locations -> drawing_plan -> drawing_set -> get project id
            ->leftJoin('drawing_plans', 'drawing_plans.id', '=', 'locations.drawing_plan_id')
            ->leftJoin('drawing_sets', 'drawing_sets.id', '=', 'drawing_plans.drawing_set_id')
            ->whereNull('setting_issues.deleted_at')
            ->whereNull('setting_types.deleted_at')
            ->whereNull('setting_category.deleted_at')
            ->select($field)
            ->groupBy('issue_id')
            ->orderBy('sum', 'desc');

        return $setting_issues;
    }
}
