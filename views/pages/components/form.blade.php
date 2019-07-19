<?php
    /**
     * @var \Ampere\Services\Workshop\Page\Layout $layout
     * @var \Ampere\Services\Workshop\Component $component
     * @var \Ampere\Services\Workshop\Page\Assets $include
     * @var \Ampere\Services\Workshop\Form\Form $form
     * @var object $data
     */

     /*
      * Layout settings
      */
     $layout->title('Form components');

     /*
      * Header component settings
      */
     $component->show('header', [
         'title' => 'Form components',
         'subtitle' => 'Components overview'
     ]);

     $controllerClass = ampere_controller('ComponentsController');
?>

<div class="row">
    <div class="col-md-6">
        <div class="ibox">
            <div class="ibox-header">
                Input
            </div>
            <div class="ibox-content">
                {!! $form->open()->name('test') !!}

                    {!! $form->input('input_default', 'Input default')->placeholder('Default input') !!}

                    {!! $form->input('input_value', 'Input disabled')
                            ->placeholder('Default input')
                            ->value('Disabled input')
                            ->disabled()->inline()
                    !!}

                    <hr/>

                    {!! $form->input('input_value', 'Input value')
                            ->value('Some value in input')->inline()
                    !!}

                    {!! $form->input('input_error', 'Input error')
                            ->value('Input has error')
                            ->error('Some error')
                    !!}

                {!! $form->close() !!}
            </div>
        </div>
        <div class="ibox">
            <div class="ibox-header">
                Select
            </div>
            <div class="ibox-content">
                {!! $form->open() !!}

                {!!
                    $form->select('select_default', 'Select default')
                        ->options([
                            'First value', 'Second value'
                        ])->inline()
                !!}

                {!!
                    $form->select('select_error', 'Select error')
                        ->options([
                            'First value', 'Second value'
                        ])
                        ->error('Some error...')
                !!}

                {!!
                    $form->select('select_placeholder', 'Select with default placeholder')
                        ->options([
                            'First value', 'Second value'
                        ])
                        ->placeholder('Placeholder')
                !!}

                {!!
                    $form->select('select_disabled', 'Select disabled')
                        ->options([
                            'First value', 'Second value'
                        ])
                        ->disabled()
                !!}

                {!!
                    $form->select('select_multi', 'Select multi')
                        ->options([
                            'First value', 'Second value'
                        ])
                        ->multiple()
                !!}

                {!!
                    $form->select('select_tags', 'Select tags')
                        ->options([
                            'First value', 'Second value'
                        ])
                        ->tags()
                        ->multiple()
                !!}

                {!!
                    $form->select('select_source', 'Select source')
                        ->tags()
                        ->multiple()
                        ->placeholder('Select from source')
                        ->source($controllerClass::route('search'))
                !!}

                {!! $form->close() !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="ibox">
            <div class="ibox-header">
                Checkbox & Radio
            </div>
            <div class="ibox-content">
                {!! $form->open() !!}
                    {!!
                        $form->radio('radio_buttons', 'Some radio')
                            ->items([
                                'new' => 'New article',
                                'draft' => 'Draft article',
                                'published' => 'Published article'
                            ])
                            ->value('draft')
                    !!}

                    {!!
                        $form->checkbox('checkbox_item', 'Status checked')
                            ->title('Active')
                            ->checked()
                    !!}

                    {!!
                        $form->checkbox('checkbox_item2', 'Status unchecked')
                            ->title('Active')
                    !!}

                {!! $form->close() !!}
            </div>
        </div>

        <div class="ibox">
            <div class="ibox-header">
                Checkbox & Radio
            </div>
            <div class="ibox-content">
                {!! $form->open() !!}
                    {!!
                        $form->textarea('textarea', 'About')
                            ->placeholder('Enter information about...')
                    !!}

                {!! $form->close() !!}
            </div>
        </div>
    </div>
</div>

