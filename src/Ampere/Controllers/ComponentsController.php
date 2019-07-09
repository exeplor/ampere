<?php

namespace Ampere\Controllers;

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
        return $this->render('components.charts', $charts);
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