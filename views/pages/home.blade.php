<?php
    /**
     * @var \Ampere\Services\Workshop\Page\Layout $layout
     * @var \Ampere\Services\Workshop\Component $component
     * @var \Ampere\Services\Workshop\Page\Assets $include
     * @var \Ampere\Services\Workshop\Form\Form $form
     * @var object $data
     */
?>

@php($layout->title('Ampere admin'))

@php($component->show('header', [
    'title' => 'Welcome to Ampere',
    'subtitle' => 'Home page',
    'buttons' => []
]))

<div class="row">
    <div class="col-md-12">
        <div class="ibox">
            <div class="ibox-body">
                Welcome to Ampere home page. <br>
                You can find this template in <b>views/pages/home.blade.php</b>
            </div>
        </div>
    </div>
</div>

