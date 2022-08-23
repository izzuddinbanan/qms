<?php

namespace App\Http\Controllers\Board;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\FilterService;
use App\Entity\Project;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PDF;
use App\Entity\GroupContractor;

class ContractorController extends Controller
{
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
        $input = $request->all();

        $tabs = [
            'All' => 'all',
            'With New Issues' => 'new_issues',
            'With Pending Start Issues' => 'pending_start_issues',
            'With WIP Issues' => 'wip_issues',
            'With Overdue Issues' => 'overdue_issues',
            'With Completed Issues' => 'completed_issues',
        ];

        return view('board.contractor.index', compact('tabs'));
    }

    public function getCount(Request $request)
    {
        $param = [];
        $param['project_id'] = session('project_id');

        $contractor = (new FilterService())->generateContractorSummaryQuery($param)->get();

        return [
            'all' => count($contractor),
            'new_issues' => $contractor ? $contractor->where('new_issues', '>', 0)->count() : 0,
            'pending_start_issues' => $contractor ? $contractor->where('pending_start_issues', '>', 0)->count() : 0,
            'wip_issues' => $contractor ? $contractor->where('wip_issues', '>', 0)->count() : 0,
            'overdue_issues' => $contractor ? $contractor->where('overdue_issues', '>', 0)->count() : 0,
            'completed_issues' => $contractor ? $contractor->where('completed_issues', '>', 0)->count() : 0,
            'issues_less_than_7_days' => $contractor ? $contractor->where('issues_less_than_7_days', '>', 0)->count() : 0,
            'issues_7_to_14_days' => $contractor ? $contractor->where('issues_7_to_14_days', '>', 0)->count() : 0,
            'issues_15_to_22_days' => $contractor ? $contractor->where('issues_15_to_22_days', '>', 0)->count() : 0,
            'issues_23_to_30_days' => $contractor ? $contractor->where('issues_23_to_30_days', '>', 0)->count() : 0,
            'issues_more_than_30_days' => $contractor ? $contractor->where('issues_more_than_30_days', '>', 0)->count() : 0
        ];
    }

    public function getListing(Request $request)
    {
        $project = Project::find(session('project_id'));
        $param = $request->all();
        $param['project_id'] = session('project_id');
        $current_page = $request->get('page');
        
        $contractor = (new FilterService())->generateContractorSummaryQuery($param)->get();
        $total = $contractor->count();
        $per_page = 10;

        $contractors = new LengthAwarePaginator($contractor->slice($per_page * ($current_page - 1))->take($per_page), $total, $per_page);
        
        return view('board.contractor.listing', compact('contractors', 'project'));
    }

    public function export(Request $request)
    {
        $param = $request->all();
        $param['project_id'] = session('project_id');
        $contractors = (new FilterService())->generateContractorSummaryQuery($param)->get();

        $project = Project::find(session('project_id'));
        $alphas = range('A', 'Z');
        $filename = "{$project->name}_contractors";
        $spreadsheet_table_header = [
            'contractor_name', 'new_issues', 'pending_start_issues', 'wip_issues', 
            'completed_issues', 
            // 'total',
            'issues_less_than_7_days', 'issues_7_to_14_days',
            'issues_15_to_22_days', 'issues_23_to_30_days',
            'issues_more_than_30_days'
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        foreach ($spreadsheet_table_header as $key => $title) {
            $sheet->setCellValue("{$alphas[$key]}1", ucwords(implode(" ", explode('_', $title))));
        }

        $current_index = 2;
        foreach ($contractors as $contractor) {
            if ($contractor->id) {
                $contractor = (array) $contractor;
                foreach ($spreadsheet_table_header as $key => $h_val) {
                    switch ($h_val) {
                        case 'new_issues':
                        case 'pending_start_issues':
                        case 'wip_issues':
                        case 'completed_issues':
                        case 'issues_less_than_7_days':
                        case 'issues_7_to_14_days':
                        case 'issues_15_to_22_days':
                        case 'issues_23_to_30_days':
                        case 'issues_more_than_30_days':
                            $data = $contractor[$h_val] ? $contractor[$h_val] : '0';
                            break;
                        // case 'total':
                        //     $wip_count = $contractor['wip_issues'] ? $contractor['wip_issues'] : '0';
                        //     $completed_count = $contractor['completed_issues'] ? $contractor['completed_issues'] : '0';
                        //     $data = $wip_count + $completed_count;
                        //     break;
                        default:
                            $data = $contractor[$h_val] ? $contractor[$h_val] : 'N/A';
                            break;
                    }

                    $sheet->setCellValue("{$alphas[$key]}{$current_index}", $data);
                }
                
                $current_index++;
            }
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

    public function getContractorIssueListing(Request $request, $id)
    {
        $contractor = GroupContractor::find($id);
        $project = Project::find(session('project_id'));

        $param = $request->all();
        $param['contractor'] = $id;
        $param['project_id'] = session('project_id');

        $issues = $this->getIssueListing($param)->orderBy('issues.created_at', 'desc')->paginate(10);

        return view('board.contractor.issue', compact('issues', 'contractor', 'project'));
    }

    public function getContractorIssueCount(Request $request, $id)
    {
        $param = $request->all();
        $param['project_id'] = session('project_id');

        $service = new FilterService();
        $count = $service->generateContractorSummaryQuery($param)
            ->where('groups.id', $id)->first();

        return [
            'new_issues' => $count ? $count->new_issues : 0,
            'pending_start_issues' => $count ? $count->pending_start_issues : 0,
            'wip_issues' => $count ? $count->wip_issues : 0,
            'overdue_issues' => $count ? $count->overdue_issues : 0,
            'completed_issues' => $count ? $count->completed_issues : 0,
            'closed_issues' => $count ? $count->closed_issues : 0,
        ];
    }

    public function show($id)
    {
        $tabs = [
            'new_issues' => 'New',
            'pending_start_issues' => 'Pending Start',
            'wip_issues' => 'Work In Progress',
            'overdue_issues' => 'Overdue',
            'completed_issues' => 'Completed',
            'closed_issues' => 'Closed',
        ];

        $current_active = 'new_issues';

        $contractor = GroupContractor::find($id);
        $project = Project::find(session('project_id'));

        $defect_type = \DB::table('category_project')
            ->join('setting_category', 'setting_category.id', 'category_project.category_setting_id')
            ->join('setting_types', 'setting_types.category_id', 'setting_category.id')
            ->join('setting_issues', 'setting_issues.type_id', 'setting_types.id')
            ->where('category_project.project_id', $project->id)
            ->whereNull('category_project.deleted_at')
            ->select([
                'setting_category.id as category_id',
                'setting_category.name as category',
                'setting_types.id as type_id',
                'setting_types.name as type',
                'setting_issues.id as issue_id',
                'setting_issues.name as issue',
            ])
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

        return view('board.contractor.show', compact('tabs', 'contractor', 'current_active', 'project', 'unit'));
    }

    public function exportContractorIssues(Request $request, $id)
    {
        $client_detail = \DB::table('projects')
            ->join('clients', 'projects.client_id', 'clients.id')
            ->where('projects.id', session('project_id'))
        ->select([
            'projects.id as id',
            'projects.name as project',
            'clients.name as client',
            'clients.logo as logo',
        ])->first();

        $client_detail->logo = $client_detail->logo ? url('/uploads/client_logo/' . $client_detail->logo) : null;

        $contractor = GroupContractor::find($id);

        $param = $request->all();
        $param['project_id'] = $client_detail->id;
        $param['contractor'] = $id;

        $type = $param['type'];
        unset($param['type']);

        $issues = $this->getIssueListing($param)->get();

        $filename = $contractor->display_name . '-' . $client_detail->project;


        if ($type == 'excel') {
            $alphas = range('A', 'Z');
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
        } else {
            $issues_detail = [];
            
            foreach ($issues as $issue) {
                $holder = [
                    'creation_date' => $issue->creation_date,
                    'remarks' => $issue->remarks ? $issue->remarks : 'N/A'
                ];

                $holder['detail'] = [
                    'reference' => $issue->reference ? $issue->reference : 'N/A',
                    'location' => $issue->location,
                    'type' => $issue->category,
                    'description' => $issue->type . ' - ' . $issue->issue,
                    'status' => $issue->status,
                    'created' => $issue->created_by_id ? $issue->creation_date . ' by ' . $issue->created_by : 'N/A',
                    'confirmed' => $issue->confirmed_by_id ? $issue->confirmation_date . ' by ' . $issue->confirmed_by : 'N/A',
                ];

                if ($issue->status_id == 10 || $issue->status_id == 8) {
                    if ($issue->status_id == 10) {
                        $holder['detail']['closed'] = $issue->closed_by_id ? $issue->closing_date . ' by ' . $issue->closed_by : 'N/A';
                    }

                    $holder['detail']['completed'] = $issue->completed_by_id ? $issue->completion_date . ' by ' . $issue->completed_by : 'N/A';
                } else {
                    $holder['detail']['target_completion_date'] = $issue->target_completion_date;
                }

                if ($issue->contractor_id) {
                    $holder['detail']['contractor'] = $issue->contractor;
                }

                $holder['photo'] = [];
                if ($issue->before_image_file) {
                    $holder['photo'][] = [
                        'image' => url('/uploads/issues/thumbnail/' . $issue->before_image_file),
                        'created_at' => (new Carbon($issue->before_image_time))->format('d/m/Y, h:i a')
                    ];
                }
        
                if ($issue->after_image_file) {
                    $holder['photo'][] = [
                        'image' => url('/uploads/issues/thumbnail/' . $issue->after_image_file),
                        'created_at' => (new Carbon($issue->after_image_time))->format('d/m/Y, h:i a')
                    ];
                }

                $issues_detail[] = $holder;
            }
            
            $pdf = PDF::loadView('pdf.contractor', compact('client_detail', 'issues_detail', 'contractor'));

            return $pdf->download($filename . '.pdf');
        }
    }

    public function getIssueListing($param)
    {
        $status = $param['status'];
        unset($param['status']);

        $service = new FilterService();
        $issues = $service->generateIssueQuery($param);

        switch ($status) {
            case 'new_issues':
                $issues = $issues->where('status.internal', 'New');       
                break;
            case 'pending_start_issues':
                $issues = $issues->where('status.internal', 'Pending Start');       
                break;
            case 'wip_issues':
                $issues = $issues->where('status.internal', 'W.I.P');
                break;
            case 'overdue_issues':
                $issues = $issues->where('status.internal', 'W.I.P')->where('issues.due_by', '<', Carbon::today()->format('Y-m-d'));
                break;
            case 'completed_issues':
                $issues = $issues->where('status.internal', 'Completed');
                break;
            case 'closed_issues':
                $issues = $issues->where('status.internal', 'Closed');
                break;
        }

        return $issues->select($service->fields);
    }
}
