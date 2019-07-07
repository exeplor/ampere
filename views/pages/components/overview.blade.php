<?php
    /**
     * @var \Ampere\Services\WorkshopOld\Layout $layout Set name of target layout (can by "empty" or "default")
     * @var \Ampere\Services\WorkshopOld\Form\Form $form Form builder
     * @var \Ampere\Services\WorkshopOld\Component $component
     * @var object $data Custom data
     */

     /*
      * Layout settings
      */
     $layout->title('Components overview');

     /*
      * Header component settings
      */
     $component->show('header', [
         'title' => 'Components overview',
         'subtitle' => 'Overview',
         'buttons' => [

         ]
     ])
?>

<div class="ibox">
    <div class="ibox-body">
        Hi
    </div>
</div>

