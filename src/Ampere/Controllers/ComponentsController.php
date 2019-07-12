<?php

namespace Ampere\Controllers;

use Ampere\Services\Chart\Chart;
use Illuminate\Http\Request;

/**
 * Class ComponentsController
 * @menu Components
 */
class ComponentsController extends Controller
{
    /**
     * @menu General
     */
    public function index()
    {
        return $this->render('components.overview');
    }

    /**
     * @menu Form
     */
    public function form()
    {
        return $this->render('components.form');
    }

    /**
     * @menu Charts
     */
    public function charts()
    {
        $charts = [];
        $charts['first'] = Chart::build('first', function(Chart $chart){
            $chart->addDateRangeFilter('2018-01-01', '2019-01-01');

            $chart->addSelectFilter('Group By', 'group', [
                '1d' => 'Group by: One day',
                '1w' => 'Group by: One week',
                '1m' => 'Group by: One month',
            ], '1d');

            // $chart->filter->getStartDate();
            // $chart->filter->getEndDate();
            // $chart->filter->has('group');
            // $chart->filter->getValueOf('group')

            $chart->options([
                // 'fill' => true,
                'border' => 2,
                'showSum' => true
            ]);

            $chart->add('Conversion', [
                '01/01' => 5,
                '01/02' => rand(10, 30),
                '01/03' => rand(10, 30),
                '01/06' => rand(10, 30),
                '01/07' => rand(10, 30),
                '01/08' => rand(10, 30),
            ]);

            $chart->add('Views', [
                '01/01' => 10,
                '01/02' => 20,
                '01/03' => 16,
                '01/06' => 26,
                '01/07' => 30,
                '01/08' => 28,
            ]);
        });

        $charts['second'] = Chart::build('second', function(Chart $chart){
            $chart->addDateRangeFilter('2018-01-01', '2019-01-01');

            $chart->options([
                'fill' => true
            ]);

            $chart->bar();

            $chart->add('Conversion', [
                '01/01' => 5,
                '01/02' => rand(10, 30),
                '01/03' => rand(10, 30),
                '01/06' => rand(10, 30),
                '01/07' => rand(10, 30),
                '01/08' => rand(10, 30),
                '01/09' => rand(10, 30),
                '01/10' => rand(10, 30),
            ]);
        });

        return $this->render('components.charts', compact('charts'));
    }

    /**
     * @post form
     */
    public function search()
    {
        $this->validate([
            'query' => 'required',
            'field' => 'required'
        ]);

        $data = [
            'first' => 'First',
            'second' => 'Second',
            'third' => 'Third',
            'forth' => 'Forth',
            'five' => 'Five',
            'six' => 'Six',
            'seven' => 'Seven',
            'another' => 'Another'
        ];

        $result = [];
        foreach($data as $key => $title) {
            if (preg_match('/' . preg_quote($this->request->input('query')) . '/i', $key)) {
                $result[] = [
                    'id' => $key,
                    'text' => $title
                ];
            }
        }

        return response()->json([
            'results' => $result
        ]);
    }
}