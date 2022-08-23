<?php

namespace App\Http\Controllers\Ajax;

use Analytics;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Analytics\Period;
use App\Entity\FirebaseAnalytic;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
    /**
     * @return mixed
     */
    public function fetchUserPerOS()
    {
        $period = Period::create(Carbon::parse('first day of January'), Carbon::parse('last day of December'));
        $response = Analytics::performQuery(
            $period,
            'ga:users',
            [
                'dimensions' => 'ga:operatingSystem, ga:country',
                'filters'    => 'ga:country==Malaysia',
            ]
        );

        $data = collect($response['rows'] ?? [])->map(function (array $operatingSystem) {
            if ($operatingSystem[0] != 'Linux') {

                return [
                    'os'      => $operatingSystem[0],
                    'labels'  => $operatingSystem[2],
                    'country' => $operatingSystem[1],
                    'users'   => (int) $operatingSystem[2],
                ];
            }
        });

        return response()->json(compact('data'));
    }

    /**
     * @param $date
     */
    public function fetchUsersAnnually()
    {
        $period = Period::create(Carbon::parse('first day of January'), Carbon::parse('last day of December'));
        $response = Analytics::performQuery(
            $period,
            'ga:users',
            [
                'dimensions' => 'ga:month',
            ]
        );

        $data = collect($response->rows ?? [])->map(function (array $userRow) {
            return [
                'month' => $userRow[0],
                'users' => (int) $userRow[1],
            ];
        });

        return response()->json(compact('data'));
    }

    public function fetchNewUsersAnnually()
    {
        $period = Period::create(Carbon::parse('first day of January'), Carbon::parse('last day of December'));
        $response = Analytics::performQuery(
            $period,
            'ga:newUsers',
            [
                'dimensions' => 'ga:month',
            ]
        );

        $data = collect($response->rows ?? [])->map(function (array $userRow) {
            return [
                'month' => $userRow[0],
                'users' => (int) $userRow[1],
            ];
        });
        return response()->json(compact('data'));
    }

    /**
     * @return mixed
     */
    public function fetchUserPerSeason()
    {

        $total = 0;
        $data = collect([]);
        $users = collect($this->fetchUsersAnnually()->getData(true)['data']);

        $users->chunk(3)->each(function ($value) use ($data) {
            $total = 0;
            $total = $value->sum('users');

            $data->push($total);
        });

        return response()->json(compact('data'));
    }

    /**
     * @param Request $request
     */
    public function fetchUsersByDate(Request $request)
    {

        try {
            if ($request->input('data.start') != '' && $request->input('data.end') != '') {

                $period = Period::create(Carbon::createFromFormat('d/m/Y', $request->input('data.start')), Carbon::createFromFormat('d/m/Y', $request->input('data.end')));
                $response = Analytics::performQuery(
                    $period,
                    'ga:users',
                    [
                        'dimensions' => 'ga:day',
                    ]
                );

                $data = collect($response->rows ?? [])->map(function (array $userRow) {
                    return [
                        'days'  => $userRow[0],
                        'users' => (int) $userRow[1],
                    ];
                });
                return response()->json(compact('data'));

            }

        } catch (\Spatie\Analytics\Exceptions\InvalidPeriod $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => true]);
        }

    }

    /**
     * @param Request $request
     */
    public function fetchNewUsersByDate(Request $request)
    {

        try {
            if ($request->input('data.start') != '' && $request->input('data.end') != '') {

                $period = Period::create(Carbon::createFromFormat('d/m/Y', $request->input('data.start')), Carbon::createFromFormat('d/m/Y', $request->input('data.end')));
                $response = Analytics::performQuery(
                    $period,
                    'ga:newUsers',
                    [
                        'dimensions' => 'ga:day',
                    ]
                );

                $data = collect($response->rows ?? [])->map(function (array $userRow) {
                    return [
                        'days'  => $userRow[0],
                        'users' => (int) $userRow[1],
                    ];
                });
                return response()->json(compact('data'));

            }

        } catch (\Spatie\Analytics\Exceptions\InvalidPeriod $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => true]);
        }

    }

    /**
     * @param Request $request
     */
    public function fetchOSByDate(Request $request)
    {
        try {

            if ($request->input('data.start') != '' && $request->input('data.end') != '') {

                $period = Period::create(Carbon::createFromFormat('d/m/Y', $request->input('data.start')), Carbon::createFromFormat('d/m/Y', $request->input('data.end')));
                $response = Analytics::performQuery(
                    $period,
                    'ga:users',
                    [
                        'dimensions' => 'ga:operatingSystem, ga:country',
                        'filters'    => 'ga:country==Malaysia',
                    ]
                );

                $data = collect($response['rows'] ?? [])->map(function (array $operatingSystem) {
                    if ($operatingSystem[0] != 'Linux') {

                        return [
                            'os'      => $operatingSystem[0],
                            'labels'  => $operatingSystem[2],
                            'country' => $operatingSystem[1],
                            'users'   => (int) $operatingSystem[2],
                        ];
                    }
                });
                return response()->json(compact('data'));

            }
        } catch (\Spatie\Analytics\Exceptions\InvalidPeriod $e) {
            // return response()->json(['message' => $e->getMessage(), 'error' => true]);
        }
    }

    /**
     * @return mixed
     */
    public function getTotalVisitorAnnually()
    {
        $analytics = FirebaseAnalytic::currentYear()->get();
        $data = collect();
        $result = collect();
        $analytics->each(function ($platform) use ($data, $result) {
            if ($data->isEmpty()) {
                # code...
                $data->push($platform->user_per_month);
            } else {
                $result->put('data', sum_between_two_arrays($data->first(), $platform->user_per_month));
            }
        });

        return response()->json([
            'data' => $result['data'],
        ]);
    }

    public function getVisitorByPlatform()
    {
        $analytics = FirebaseAnalytic::currentYear()->get();
        $data = collect();

        $analytics->each(function ($platform) use ($data) {
            $total = collect($platform->user_per_month)->sum();
            $data->push([
                'os'     => $platform->platform,
                'labels' => (string) $total,
                'users'  => $total,
            ]);
        });

        return response()->json(compact('data'));
    }
}
