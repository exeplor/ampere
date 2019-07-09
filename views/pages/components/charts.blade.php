<?php
    /**
     * @var \Ampere\Services\Workshop\Layout $layout Set name of target layout (can by "empty" or "default")
     * @var \Ampere\Services\Workshop\Form\Form $form Form builder
     * @var \Ampere\Services\Workshop\Component $component
     * @var object $data Custom data
     */

     /*
      * Layout settings
      */
     $layout->title('Charts');

     /*
      * Header component settings
      */
     $component->show('header', [
         'title' => 'Charts',
         'subtitle' => 'Charts overview',
         'buttons' => []
     ])
?>



