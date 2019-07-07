@if($pagination->lastPage() > 1)
    <tr>
        <td colspan="100">
            <div class="table-pagination">
                {!! $pagination->links() !!}
                <div class="total-count">
                    Founded {!! $pagination->total() !!} records
                </div>
            </div>
        </td>
    </tr>
@endif