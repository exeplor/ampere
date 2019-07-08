<?php
    /**
     * @var string $title
     * @var string $subtitle
     * @var array $buttons
     */
?>

<div class="ibox">
    <div class="header-box">
        <div class="title">
            <h1>{{ $title }}</h1>

            @isset($subtitle)
                <small>{{ $subtitle }}</small>
            @endisset
        </div>

        @isset($buttons)
            <div class="control">
                @foreach($buttons as $button)
                    @php
                        $hasAccess = true;
                        $targetUrl = $button['action'] ?? null;

                        if (isset($button['route']) && $button['route'] instanceof \Ampere\Services\Route) {

                            /**
                             * @var \Ampere\Services\Route $route
                             */
                            $route = $button['route'];

                            $hasAccess = $route->access();
                            $targetUrl = $route->url();
                        }
                    @endphp
                    @if($hasAccess)
                        <a class="btn btn-{{ $button['type'] ?? 'primary' }}" href="{{ $targetUrl }}">
                            {!! $button['title'] !!}
                        </a>
                    @endif
                @endforeach
            </div>
        @endisset
    </div>
</div>