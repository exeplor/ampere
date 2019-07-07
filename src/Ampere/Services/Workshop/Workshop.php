<?php

namespace Ampere\Services\Workshop;

use Ampere\Facades\Ampere;
use Ampere\Services\Menu;
use Ampere\Services\Workshop\Layout\Builder;
use Ampere\Services\Workshop\Page\Layout;
use Ampere\Services\Workshop\Layout\Layout as BaseLayout;
use Illuminate\Http\Request;

/**
 * Class Workshop
 * @package Ampere\Services\Workshop
 */
class Workshop
{
    /**
     * @var Request
     */
    private $request;

    /**
     * Workshop constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $name
     * @param array $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function page(string $name, array $data = [])
    {
        $builder = new Builder();
        $builder->setRequest($this->request);

        $pageData = [
            'layout' => new Layout($builder),
            'include' => $builder->makeAssetManager(),
            'component' => $builder->makeComponent(),
            'form' => $builder->makeForm(),
            'data' => (object)$data
        ];

        $pageContent = View::render('pages.' . $name, $pageData)->render();

        $builder->setContent($pageContent);

        $layoutData = [
            'layout' => new BaseLayout($builder),
            'user' => Ampere::guard()->getUser(),
            'menu' => (new Menu())->get()
        ];

        $layoutView = View::render('layouts.' . $builder->getName(), $layoutData);
        return $layoutView;
    }
}