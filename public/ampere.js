$(function(){
    var menu = $('#menu');

    menu.find('.item > a').click(function(){
        var items = $(this).parent().children('.items');

        if (items.length > 0) {
            menu.find('.item.active').removeClass('active').children('.items').slideUp(100);
            items.slideDown(100).parent().addClass('active');
            return false;
        } else {
            return true;
        }
    });
});

function dataTable(tableId) {
    var object = $(tableId);

    var currentPage = 1,
        sortField = null,
        sortDirection = null,
        targetUrl = null,
        formName = null;

    var SORT_ASC = 'ASC',
        SORT_DESC = 'DESC';

    var timeout = null;

    $(document).on('click', tableId + ' .table-pagination a.page-link', function(){
        var page = $(this).attr('href').replace(/^.+?page=([0-9]+)/, '$1');
        currentPage = parseInt(page);
        search();
        return false;
    });

    $(document).on('keyup', tableId + ' .data-table-filter input', function(){
        clearTimeout(timeout);
        timeout = setTimeout(search, 500);
    });

    $(document).on('change', tableId + ' .data-table-filter select', function(){
        search();
    });

    $(document).on('click', tableId + ' .data-table-sortable', function(){
        var currentDirection = $(this).attr('data-direction'),
            target = $(this);

        if (!currentDirection) {
            currentDirection = SORT_ASC;
        } else {
            currentDirection = currentDirection === SORT_ASC ? SORT_DESC : SORT_ASC;
        }

        $(tableId + ' .data-table-sortable')
            .removeClass('data-table-direction-desc data-table-direction-asc').removeAttr('current-direction');

        target.addClass(currentDirection === SORT_ASC ? 'data-table-direction-asc' : 'data-table-direction-desc');
        target.attr('data-direction', currentDirection);

        sortField = target.parent().attr('data-column');
        sortDirection = currentDirection;

        search();
    });

    $(document).on('click', tableId + ' .btn[data-name=delete]', function(){
        var row = $(this).parents('tr'),
            id = row.attr('data-id');

        if (id) {
            if (confirm('Delete this record?')) {
                $.post($(this).attr('href'), {
                    id: id,
                    _token: $('meta[name=token]').attr('content'),
                    _method: 'delete'
                });
                row.remove();
            }
        }

        return false;
    });

    $(document).on('click', tableId + ' .data-table-export-button', function(){
        var query = getQuery();
        var request = {};

        query.__export = 1;
        request[formName] = query;

        $(this).attr('href', $(this).attr('data-href') + '?' + $.param(request));
    });

    var datePickers = object.find('.data-table-date-filter');
    if (datePickers.length > 0) {
        datePickers.daterangepicker({
            autoApply: true,
            showCustomRangeLabel: true,
            alwaysShowCalendars: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            },
            format: 'YYYY-MM-DD',
            separator : ' - '
        });

        datePickers.on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            search();
        });
    }

    function search() {
        var query = getQuery();

        var request = {};
        request[formName] = query;

        $.get(targetUrl, request, function(data){
            element('tbody').html(data.rows);
            element('tfoot').html(data.pagination);
        });
    }

    function getQuery() {
        var query = {
            sort: {
                field: sortField,
                direction: sortDirection
            },
            filter: {},
            page: currentPage
        }

        object.find('.filter-box input, .filter-box select').map(function(){
            var input = $(this),
                field = input.parent().attr('data-column'),
                value = input.val();

            if (value && value.length > 0) {
                query.filter[field] = value;
            }
        });

        return query;
    }

    function element(name) {
        return $(tableId + ' ' + name);
    }

    return new function() {
        this.url = function(url) {
            targetUrl = url;
            return this;
        }

        this.form = function(form) {
            formName = form;
            return this;
        }
    }
}

function updateChart(name, field, value)
{
    var object = $('.chart-container[data-chart-name=' + name + ']');
    var filter = object.attr('data-filter') ? JSON.parse(object.attr('data-filter')) : {};

    filter[field] = value;

    object.attr('data-filter', JSON.stringify(filter));

    var data = {};
    data['chart__' + name] = filter;

    $.get('', data, function(content){
        object.html(content);
    });

}