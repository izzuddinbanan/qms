<?php

namespace App\Http\Controllers\Board;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Entity\Project;
use Carbon\Carbon;
use App\Services\FilterService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Entity\Status;
use PDF;
use App\Entity\LocationPoint;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\Controller;

class CommonAreaController extends Controller
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

        $active = isset($input['active']) && $input['active'] ? $input['active'] : 'all';

        $tabs = [
            'All' => 'all',
            'With New Issues' => 'new_issues',
            'With WIP Issues' => 'wip_issues',
            'With Completed Issues' => 'completed_issues',
            'With Closed Issues' => 'closed_issues',
        ];

        $second_tabs = [
            '< 7 Days' => 'issues_less_than_7_days',
            '7 - 14 Days' => 'issues_7_to_14_days',
            '15 - 22 Days' => 'issues_15_to_22_days',
            '23 - 30 Days' => 'issues_23_to_30_days',
            '> 30 Days' => 'issues_more_than_30_days',
        ];

        $unit = \DB::table('drawing_sets')
            ->join('drawing_plans', 'drawing_sets.id', 'drawing_plans.drawing_set_id')
            ->where('drawing_sets.project_id', session('project_id'))
            ->whereNull('drawing_sets.deleted_at')
            ->whereNotNull('drawing_plans.block')
            ->whereNotNull('drawing_plans.level')
            ->whereNotNull('drawing_plans.unit')
            ->whereIn('drawing_plans.types', ['common'])
            ->select(['drawing_plans.id', 'drawing_plans.block', 'drawing_plans.level', 'drawing_plans.unit'])
            ->get();

        $type = 'common';

        return view('board.unit.index', compact('tabs', 'second_tabs', 'active', 'unit', 'type'));
    }

    public function getCount(Request $request)
    {
        $param = $request->all();
        $param['type'] = 'common';
        $param['project_id'] = session('project_id');

        $issues = (new FilterService())->generateUnitSummaryQuery($param)->get();

        return [
            'all' => count($issues),
            'new_issues' =>  $issues ? $issues->where('new_issues', '>', 0)->count() : 0,
            'wip_issues' => $issues ? $issues->where('wip_issues', '>', 0)->count() : 0,
            'completed_issues' => $issues ? $issues->where('completed_issues', '>', 0)->count() : 0,
            'closed_issues' => $issues ? $issues->where('closed_issues', '>', 0)->count() : 0,
            'issues_less_than_7_days' => $issues ? $issues->where('issues_less_than_7_days', '>', 0)->count() : 0,
            'issues_7_to_14_days' => $issues ? $issues->where('issues_7_to_14_days', '>', 0)->count() : 0,
            'issues_15_to_22_days' => $issues ? $issues->where('issues_15_to_22_days', '>', 0)->count() : 0,
            'issues_23_to_30_days' => $issues ? $issues->where('issues_23_to_30_days', '>', 0)->count() : 0,
            'issues_more_than_30_days' => $issues ? $issues->where('issues_more_than_30_days', '>', 0)->count() : 0
        ];
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $unit = \DB::table('drawing_plans')
            ->join('drawing_sets', 'drawing_sets.id', '=', 'drawing_plans.drawing_set_id')
            ->join('projects', 'drawing_sets.project_id', 'projects.id')
            ->where('drawing_plans.id', $id)
            ->select([
                'drawing_plans.id as id',
                'drawing_plans.block as block',
                'drawing_plans.level as level',
                'drawing_plans.unit as unit',
                'drawing_plans.file as drawing_plan_image',
                'drawing_plans.height as drawing_plan_file_height',
                'drawing_plans.width as drawing_plan_file_width',
                'projects.name as project',
            ])->first();

        $status = Status::select('id', 'internal')->get()->pluck('internal', 'id');
        $location = LocationPoint::where('drawing_plan_id', $id)->get()->pluck('name', 'id');

        $contractor = \DB::table('group_project')
            ->join('groups', 'groups.id', 'group_project.group_id')
            ->whereNull('group_project.deleted_at')
            ->where('group_project.project_id', session('project_id'))
            ->select('groups.*')
            ->get()->pluck('display_name', 'id');

        $unit->drawing_plan_image = url('/uploads/drawings/' . $unit->drawing_plan_image);
        $type = 'common';

        return view('board.unit.show', compact('unit', 'status', 'location', 'contractor', 'type'));
    }

    public function getListing(Request $request)
    {
        $project = Project::find(session('project_id'));

        $param = $request->all();
        $param['type'] = $type = 'common';
        $param['project_id'] = session('project_id');
        $current_page = $request->get('page');
        
        $issues = (new FilterService())->generateUnitSummaryQuery($param)->get();
        $total = $issues->count();
        $per_page = 10;

        $issues = $issues->slice($per_page * ($current_page - 1))->take($per_page);
        $issues = new LengthAwarePaginator($issues, $total, $per_page);
        
        return view('board.unit.listing', compact('issues', 'project', 'type'));
    }

    public function export(Request $request)
    {
        $param = $request->all();
        $param['type'] = 'common';
        $param['project_id'] = session('project_id');
        $issues = (new FilterService())->generateUnitSummaryQuery($param)->get();

        $project = Project::find(session('project_id'));
        $alphas = range('A', 'Z');
        $filename = $project->name . '_common_areas';
        $spreadsheet_table_header = [
            'block', 'level', 'unit', 'owner', 'new_issues',
            'wip_issues', 'completed_issues', 'closed_issues',
            'date_of_notice', 'date_handed_overs', 'dlp_start', 'dlp_end', 'ref',
            'issues_less_than_7_days', 'issues_7_to_14_days', 'issues_15_to_22_days',
            'issues_23_to_30_days', 'issues_more_than_30_days'
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        foreach ($spreadsheet_table_header as $key => $title) {
            $sheet->setCellValue("{$alphas[$key]}1", ucwords(implode(" ", explode('_', $title))));
        }

        $current_index = 2;
        foreach ($issues as $issue) {
            if ($issue->id) {
                $issue = (array) $issue;
                foreach ($spreadsheet_table_header as $key => $h_val) {
                    switch ($h_val) {
                        case 'new_issues':
                        case 'wip_issues':
                        case 'completed_issues':
                        case 'closed_issues':
                        case 'issues_less_than_7_days':
                        case 'issues_7_to_14_days':
                        case 'issues_15_to_22_days':
                        case 'issues_23_to_30_days':
                        case 'issues_more_than_30_days':
                            $data = $issue[$h_val] ? $issue[$h_val] : '0';
                            break;
                        default:
                            $data = isset($issue[$h_val]) ? $issue[$h_val] != '00/00/0000' ? $issue[$h_val] : 'N/A' : 'N/A';
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
}
