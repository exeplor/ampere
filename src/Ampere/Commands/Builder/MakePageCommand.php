<?php

namespace Ampere\Commands\Builder;

use Ampere\Commands\Command;
use Ampere\Services\StubBuilder;

class MakePageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'am:page {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make page view';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param StubBuilder $stubBuilder
     */
    public function handle(StubBuilder $stubBuilder)
    {
        $pageName = $this->argument('name');

        $targetTemplatePath = ampere_prefix('/pages/' . $pageName . '.blade.php');
        $targetPath = resource_path('views/' . $targetTemplatePath);

        $stubBuilder->setStub('page')
            ->setTargetPath($targetPath);

        $params = [];

        $requiredParams = ['title', 'subtitle'];
        foreach($requiredParams as $key) {
            $params[$key] = $this->ask('Enter page ' . $key);
        }

        $stubBuilder->setParams($params);

        if ($stubBuilder->isFileExists()) {
            $this->error('Page ' . $pageName . ' already exists');
            $answer = $this->ask('You really want to rewrite this file? (enter "yes" for accept)');

            if ($answer !== 'yes') {
                $this->comment('Page not created');
                return;
            }
        }

        if ($stubBuilder->create()) {
            $this->comment('Page ' . $pageName . ' created');
        }
    }
}
