<?php
    /**
     * @var \Ampere\Services\Workshop\Layout\Layout $layout
     */
?>

<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge;" />

        <meta name="token" content="{{ csrf_token() }}">

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,400i,700,700i&display=swap" rel="stylesheet">
        <link href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" rel="stylesheet">

        @php($layout->css('vendor/select2/css/select2.min.css'))
        @php($layout->css('vendor/toastr/toastr.min.css'))
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

        @php($layout->js('vendor/select2/js/select2.min.js'))
        @php($layout->js('vendor/toastr/toastr.min.js'))
        @php($layout->js('vendor/chart/chart.js'))
        @php($layout->js('ampere.js'))

        <title>{!! $layout->getTitle() !!}</title>
    </head>
    <body>
        <div class="main-container">
            <div id="menu">
                <div class="logo">
                    AMPERE
                </div>

                <div class="profile">
                    <img src="{{ ampere_public_path('images/face.png') }}">
                    <div class="info">
                        <div class="name">{{ $user->roles->first()->title }}</div>
                        <div class="title">{{ $user->email }}</div>
                    </div>
                </div>
                <div class="items">
                    @foreach($menu as $item)
                        <div class="item{{ $item['is_active'] ? ' active' : null }}">
                            <a href="{{ $item['link'] ?: '#' }}">
                                @if(!empty($item['icon']))
                                    <i class="fa fa-{{ $item['icon'] }}"></i>
                                @endif
                                <span>{{ $item['title'] }}</span>
                            </a>

                            @if(count($item['child']) > 0)
                                <div class="items">
                                    @foreach($item['child'] as $child)
                                        <div class="item{{ $child['is_active'] ? ' active' : null }}">
                                            <a href="{{ $child['link'] }}">{{ $child['title'] }}</a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="content">
                {!! $layout->getContent() !!}
            </div>

            <script>
                toastr.options.showMethod = 'slideDown';
                toastr.options.hideMethod = 'fadeOut';
                toastr.options.closeMethod = 'fadeOut';
                toastr.options.progressBar = true;
                toastr.options.hideDuration = 200;
                toastr.options.showDuration = 0;
            </script>

            @if($message = session('success'))
                <script>
                    toastr.success('{{ $message }}', 'Success');
                </script>
            @endif

            @if($message = session('warning'))
                <script>
                    toastr.warning('{{ $message }}', 'Warning');
                </script>
            @endif

            @if($message = session('error'))
                <script>
                    toastr.error('{{ $message }}', 'Error');
                </script>
            @endif
        </div>
    </body>
</html>