<?php
    /**
     * @var \Ampere\Services\Workshop\Layout $layout
     */
?>

<html>
    <head>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

        @php($layout->css('ampere.css'))

        @foreach($layout->getCustomCss() as $css)
            <link rel="stylesheet" href="{{ $css }}">
        @endforeach

        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

        @foreach($layout->getCustomJs() as $js)
            <script src="{{ $js }}"></script>
        @endforeach

        <title>{!! $layout->getTitle() !!}</title>
    </head>
    <body>
        <div class="main-container">
            <div id="content">
                {!! $layout->getContent() !!}
            </div>
        </div>
    </body>
</html>