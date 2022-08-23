<?php

namespace App\Http\Controllers\Board;

use Auth;
use Validator;
use Helper;
use App\Entity\Project;
use App\Entity\DrawingSet;
use App\Entity\RoleUser;
use App\Entity\User;
use App\Entity\DrawingPlan;
use App\Entity\LocationPoint;
use App\Entity\Issue;
use App\Entity\History;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\PushNotification;
use App\Services\FilterService;
use App\Entity\Status;
use App\Entity\SettingPriority;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PDF;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class IssueController extends Controller
{
    use PushNotification;

    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $project = session('project_id');

        $defect_type = \DB::table('category_project')
            ->join('setting_category', 'setting_category.id', 'category_project.category_setting_id')
            ->where('category_project.project_id', $project)
            ->whereNull('category_project.deleted_at')
            ->select(['setting_category.id as id', 'setting_category.name as name'])
            ->get();

        $unit = \DB::table('drawing_plans')
            ->join('drawing_sets', 'drawing_sets.id', 'drawing_plans.drawing_set_id')
            ->where('drawing_sets.project_id', $project)
            ->whereIn('drawing_plans.types', ['unit', 'common'])
            ->select(['drawing_plans.id', 'drawing_plans.block', 'drawing_plans.level', 'drawing_plans.unit', 'drawing_plans.types'])
            ->get();

        $contractors = \DB::table('group_project')
            ->join('groups', function($join) {
                $join->on('groups.id', '=', 'group_project.group_id')
                    ->whereNull('groups.deleted_at');
            })
            ->whereNull('group_project.deleted_at')
            ->where('group_project.project_id', $project)
            ->select('groups.*')
            ->get();
        $contractors = $contractors->pluck('display_name', 'id');

        $location = \DB::table('locations')
            ->join('drawing_plans', 'drawing_plans.id', 'locations.drawing_plan_id')
            ->join('drawing_sets', 'drawing_sets.id', 'drawing_plans.drawing_set_id')
            ->where('drawing_sets.project_id', $project)
            ->select(['locations.id', 'locations.name', 'locations.reference'])
            ->get();

        $unit = \DB::table('drawing_sets')
            ->join('drawing_plans', 'drawing_sets.id', 'drawing_plans.drawing_set_id')
            ->where('drawing_sets.project_id', session('project_id'))
            ->whereNull('drawing_sets.deleted_at')
            ->whereNotNull('drawing_plans.block')
            ->whereNotNull('drawing_plans.level')
            ->whereNotNull('drawing_plans.unit')
            ->whereIn('drawing_plans.types', ['unit', 'common'])
            ->select(['drawing_plans.id', 'drawing_plans.block', 'drawing_plans.level', 'drawing_plans.unit'])
            ->get();

        $priority = \DB::table('priority_project')
            ->join('setting_priority', 'setting_priority.id', 'priority_project.priority_id')
            ->where('priority_project.project_id', $project)
            ->whereNull('priority_project.deleted_at')
            ->select([ 'setting_priority.id as id', 'setting_priority.type as type', 'setting_priority.name as name' ])->get()->pluck('type', 'id');
        $priority = collect($priority)->toArray();
        ksort($priority);

        // status
        $status = Status::get()->pluck('internal', 'id');
        
        $current_active = $request->query() ? $request->query() : [ 'status' => 0 ];
        $status[0] = 'All';
        $status = collect($status)->toArray();
        ksort($status);

        return view('board.issue.index', compact('defect_type', 'unit', 'location', 'status', 'priority', 'contractors', 'current_active'));
    }

    public function getCount(Request $request)
    {
        $param = $request->all();
        $param['project_id'] = session('project_id');

        $priority = \DB::table('priority_project')
            ->join('setting_priority', 'setting_priority.id', 'priority_project.priority_id')
            ->where('priority_project.project_id', session('project_id'))
            ->whereNull('priority_project.deleted_at')
            ->select([ 'setting_priority.id as id', 'setting_priority.type as type', 'setting_priority.name as name' ])->get()->pluck('type', 'id');

        $status = Status::get()->pluck('internal', 'id');

        $field = [
            \DB::raw('count(*) as status_0'),
            \DB::raw('count(*) as priority_0')
        ];

        foreach ($status as $key => $val) {
            $query_string = "sum(status.id = {$key}) status_{$key}";
            $field[] = \DB::raw($query_string);
        }

        if ($priority) {
            foreach ($priority as $key => $val) {
                $query_string = "sum(setting_priority.id = {$key}) priority_{$key}";
                $field[] = \DB::raw($query_string);
            }
        }
        
        $filter_service = new FilterService();
        $count = $filter_service->generateIssueQuery($param)->select($field)->first();

        return collect($count)->toArray();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!$issue = Issue::where('id', $id)
                ->with('location.drawingPlan')
                ->with('status')
                ->with('issue')
                ->with('type')
                ->with('category')
                ->with('priority')
                ->with('contractor')
                ->first()) {
            return back();
        }

        $history = History::where('issue_id', $issue->id)->with('status')->with('images')->with('user')->orderBy('id', 'DESC')->get();
        
        ##for back to list of issue by category
        if ($issue->status->id == '2') {
            $issue->status->internal = 'new';
        }
        if ($issue->status->id == '5' || $issue->status->internal == '9') {
            $issue->status->internal = 'wip';
        }
        if ($issue->status->id == '8') {
            $issue->status->internal = 'complete';
        }
        if ($issue->status->id == '4') {
            $issue->status->internal = 'reject';
        }

        $issue->location->drawingPlan->file = url('uploads/drawings') . DIRECTORY_SEPARATOR . '' . $issue->location->drawingPlan->file;

        $issue->due_by = date_format(date_create($issue->due_by), "d M Y");

        return view('board.issue.show', compact('issue', 'history'));
    }

    public function getIssueListing(Request $request)
    {
        $project = Project::find(session('project_id'));
        $param = $request->all();

        $current_active = 'status_0';
        $param['project_id'] = session('project_id');

        $filter_service = new FilterService();
        $issues = $filter_service->generateIssueQuery($param)->orderBy('issues.created_at', 'desc')->select($filter_service->fields)->paginate(10);
        
        return view('board.issue.listing', compact('issues', 'current_active', 'project'));
    }

    public function export(Request $request)
    {
        $project = Project::find(session('project_id'));

        $param = $request->all();
        $param['project_id'] = $project->id;

        $filter_service = new FilterService();
        $issues = $filter_service->generateIssueQuery($param)->select($filter_service->fields)->get();

        $alphas = range('A', 'Z');
        $filename = $project->name . '_issues';
        $spreadsheet_table_header = [
            'block', 'level', 'unit', 'reference', 'location',
            'category', 'type', 'issue', 'priority', 'priority_nb_day' ,'status', 'archived',
            'creation_date', 'created_by', 'nb_days_open', 'confirmation_date', 'confirmed_by',
            'contractor', 'target_completion_date', 'work_start_date', 'nb_days_overdue', 'completion_date', 'completed_by',
            'closing_date', 'closed_by', 'remarks'
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        foreach ($spreadsheet_table_header as $key => $title) {
            $sheet->setCellValue("{$alphas[$key]}1", $title);
        }

        $current_index = 2;
        foreach ($issues as $issue) {
            $issue = (array) $issue;
            foreach ($spreadsheet_table_header as $key => $h_val) {
                $data = 'N/A';
                if (isset($issue[$h_val])) {
                    $data = $issue[$h_val] && $issue[$h_val] != '00/00/0000' ? $issue[$h_val] : 'N/A';
                } else {
                    switch ($h_val) {
                        case 'archived':
                            $data = $issue['deleted_date'] ? 1 : 0;
                            break;
                        case 'nb_days_open':
                            $date = ($issue['completion_date'] != '00/00/0000' ? Carbon::createFromFormat($issue['completion_date']) : Carbon::now())->startOfDay();
                            $date_start = Carbon::createFromFormat('d/m/Y', $issue['creation_date'])->startOfDay();
                            $data = $date->diffInDays($date_start) + 1;
                            break;
                        case 'nb_days_overdue':
                            $date = ($issue['completion_date'] != '00/00/0000' ? Carbon::createFromFormat($issue['completion_date']) : Carbon::now())->startOfDay();
                            $date_start = Carbon::createFromFormat('d/m/Y', $issue['target_completion_date'])->startOfDay();
                            $data = $date->diffInDays($date_start) + 1;
                            break;
                        default:
                            $data = 'N/A';
                            break;
                    }
                }
                
                $sheet->setCellValue("{$alphas[$key]}{$current_index}", $data);
            }

            $current_index++;
        }
        
        $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);

        foreach ($cellIterator as $cell) {
            $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
        }

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename='$filename.xlsx'");
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        return $writer->save('php://output');
    }

    public function downloadReport(Request $request, $id)
    {
        $filter_service = new FilterService();
        $issue = $filter_service->generateIssueQuery()->select($filter_service->fields)->where('issues.id', $id)->first();

        $ratio_width = 300;
        $ratio = $issue->drawing_plan_file_width / $ratio_width;

        $output = [];
        $output['id'] = $issue->id;
        $output['client_logo'] = $issue->client_logo ? url('/uploads/client_logo/' . $issue->client_logo) : null;
        $output['client_name'] = $issue->client_name;
        $output['project'] = $issue->project_name ? $issue->project_name : 'N/A';
        $output['block'] = $issue->block ? $issue->block : 'N/A';
        $output['level'] = $issue->level ? $issue->level : 'N/A';
        $output['unit'] = $issue->unit ? $issue->unit : 'N/A';
        $output['status'] = $issue->status ? $issue->status : 'N/A';

        $output['detail'] = [];
        $output['detail'][] = [ 'key' => 'Reference', 'value' => $issue->reference ];
        $output['detail'][] = [ 'key' => 'Location', 'value' => $issue->location ? $issue->location : 'N/A' ];
        $output['detail'][] = [ 'key' => 'Type', 'value' => $issue->category ? $issue->category : 'N/A' ];
        $output['detail'][] = [ 'key' => 'Description', 'value' => $issue->type . ' - ' . $issue->issue ];
        $output['detail'][] = [ 'key' => 'Created', 'value' => $issue->creation_date . ' by ' . $issue->created_by ];
        $output['detail'][] = [ 'key' => 'Confirmed', 'value' => $issue->confirmed_by_id ? $issue->confirmation_date . ' by ' . $issue->confirmed_by : 'N/A' ];

        if ($issue->status_id == 8 || $issue->status_id == 10) {
            $output['detail'][]= [ 'key' => 'Completed', 'value' => $issue->completed_by_id ? $issue->completed_date . ' by ' . $issue->completed_by : 'N/A' ];

            if ($issue->status_id == 10) {
                $output['detail'][]= [ 'key' => 'Closed', 'value' => $issue->closed_by_id ? $issue->closing_date . ' by ' . $issue->closed_by : 'N/A' ];
            }
        } else {
            $output['detail'][]= [ 'key' => 'Target Completion Date', 'value' => $issue->target_completion_date ];
        }

        if ($issue->contractor) {
            $output['detail'][]= [ 'key' => 'Contractor', 'value' => $issue->contractor ];
        }

        $output['remarks'] = $issue->remarks ? $issue->remarks : 'N/A';
        $output['drawing_plan_image'] = url('/uploads/drawings/thumbnail/' . $issue->drawing_plan_file);
        $output['position_x'] = $issue->position_x;
        $output['position_y'] = $issue->position_y;
        $output['status_id'] = $issue->status_id;

        $output['photo'] = [];
        if ($issue->before_image_file) {
            $output['photo'][] = [
                'image' => url('/uploads/issues/thumbnail/' . $issue->before_image_file),
                'created_at' => (new Carbon($issue->before_image_time))->format('d/m/Y, h:i a')
            ];
        }

        if ($issue->after_image_file) {
            $output['photo'][] = [
                'image' => url('/uploads/issues/thumbnail/' . $issue->after_image_file),
                'created_at' => (new Carbon($issue->after_image_time))->format('d/m/Y, h:i a')
            ];
        }

        $filename = "{$issue->project_name} - Issue ID {$issue->id}.pdf";

        $issue = $output;
        
        $pdf = PDF::loadView('pdf.issue', compact('issue', 'ratio', 'ratio_width'));
    
        return $pdf->download($filename);
    }
}
