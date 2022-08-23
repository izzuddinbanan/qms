<?php

namespace App\Http\Controllers\Ajax;

use BigQuery;
use Carbon\Carbon;
use App\Entity\FirebaseAnalytic;
use App\Http\Controllers\Controller;

class BigQueryController extends Controller
{
    /**
     * @return int
     */
    public function fetchTotalVisitorAnnually()
    {

        $now = Carbon::now();
        $queryJobConfig = BigQuery::query(
            '
            SELECT
			  month,
			  SUM(monthly_visitors) AS monthly_visitors
			FROM (
			  SELECT
			    EXTRACT(MONTH
			    FROM
			      TIMESTAMP_MICROS(event.timestamp_micros)) AS month,
			    COUNT(DISTINCT user_dim.app_info.app_instance_id) AS monthly_visitors
			  FROM
			    `com_convep_babyblock_ANDROID.app_events_' . $now->year . '*`,
			    UNNEST(event_dim) AS event
			  GROUP BY
			    month
			  UNION ALL
			  SELECT
			    EXTRACT(MONTH
			    FROM
			      TIMESTAMP_MICROS(event.timestamp_micros)) AS month,
			    COUNT(DISTINCT user_dim.app_info.app_instance_id) AS monthly_visitors
			  FROM
			    `com_convep_babyBlock_IOS.app_events_' . $now->year . '*`,
			    UNNEST(event_dim) AS event
			  GROUP BY
			    month)
			GROUP BY
			  month
		'
        );

        $queryResults = BigQuery::runQuery($queryJobConfig);

        $data = collect()->times(12)->map(function ($month, $key) use ($queryResults) {

            foreach ($queryResults as $row) {
                if ($row['month'] == $month) {
                    return $row['monthly_visitors'];
                } else {
                    return 0;
                }
            }
        });

        return response()->json(compact('data'));
    }

    /**
     * @return int
     */
    public function fetchVisitorByPlatform()
    {
        $now = Carbon::now();
        $queryJobConfig = BigQuery::query(
            '
            SELECT
			  COUNT(DISTINCT android_dataset.user_dim.app_info.app_instance_id) AS Android,
			  COUNT(DISTINCT ios_dataset.user_dim.app_info.app_instance_id) AS Ios
			FROM
			  `com_convep_babyblock_ANDROID.app_events_' . $now->year . '*` AS android_dataset,
			  `com_convep_babyBlock_IOS.app_events_' . $now->year . '*` AS ios_dataset
		'
        );

        $queryResults = BigQuery::runQuery($queryJobConfig);

        $data = collect();

        foreach ($queryResults as $row) {

            foreach ($row as $key => $value) {
                $val = [
                    'os'     => $key,
                    'labels' => (string) $value,
                    'users'  => $value,
                ];
                $data->push($val);
            }
        }

        return response()->json(compact('data'));
    }

    /**
     * @return int
     */
    public function fetchVisitorByPlatformAnnually()
    {
        $now = Carbon::now();
        $queryJobConfig = BigQuery::query(
            '
            SELECT
			  month,
			  COUNT(DISTINCT android_dataset.user_dim.app_info.app_instance_id) AS Android,
			  COUNT(DISTINCT ios_dataset.user_dim.app_info.app_instance_id) AS Ios
			FROM (
			  SELECT
			    EXTRACT(MONTH
			    FROM
			      TIMESTAMP_MICROS(event.timestamp_micros)) AS month
			  FROM
			    `com_convep_babyblock_ANDROID.app_events_' . $now->year . '*`,
			    UNNEST(event_dim) AS event
			  GROUP BY
			    month
			  UNION ALL
			  SELECT
			    EXTRACT(MONTH
			    FROM
			      TIMESTAMP_MICROS(event.timestamp_micros)) AS month
			  FROM
			    `com_convep_babyBlock_IOS.app_events_' . $now->year . '*`,
			    UNNEST(event_dim) AS event
			  GROUP BY
			    month),
			  `com_convep_babyblock_ANDROID.app_events_' . $now->year . '*` AS android_dataset,
			  `com_convep_babyBlock_IOS.app_events_' . $now->year . '*` AS ios_dataset
			GROUP BY
			  month
		'
        );

        $queryResults = BigQuery::runQuery($queryJobConfig);

        $platform = collect();
        $data = collect();

        foreach ($queryResults as $row) {

            foreach ($row as $key => $value) {
                if ($key == 'Android') {
                    $val = [
                        'month' => $row['month'],
                        'total' => $value,
                    ];
                    $platform->put($key, $val);
                } elseif ($key == 'Ios') {
                    $val = [
                        'month' => $row['month'],
                        'total' => $value,
                    ];
                    $platform->put($key, $val);
                }
            }
        }

        $platform->map(function ($platform, $key) use ($data) {

            $monthly = collect()->times(12)->map(function ($month) use ($platform) {

                if ($month == $platform['month']) {
                    return $platform['total'];
                } else {
                    return 0;
                }
            });

            $data->put($key, $monthly->toArray());
        });

        return response()->json(compact('data'));
    }

    public function fetchToLocal()
    {
        $now = Carbon::now();

        $response = $this->fetchVisitorByPlatformAnnually();
        $data = $response->getData()->data;

        foreach ($data as $key => $value) {
            $analytics = FirebaseAnalytic::firstOrCreate(['year' => $now->year, 'platform' => $key]);
            $analytics->user_per_month = $value;
            $analytics->save();
        }
    }
}
