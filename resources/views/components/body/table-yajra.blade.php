<div class="container-fluid">
    <div class="row">
        <table id="{{ $id }}" class="table table-hover {{ $class }}">
            <thead>
                <tr>
                    @foreach($columns as $column => $class)
                        <td class="{{ $class }}">{{ $column }}</td>
                    @endforeach
                </tr>
            </thead>
        </table>
    </div>
</div>