@if (Session::has('success-message') || count($errors) > 0||Session::has('warning-message') || Session::has('success'))
    <script type="text/javascript">
        $(document).ready(function(){

        @if (Session::has('success-message'))
            new PNotify({
                    title: '{{ trans('main.success') }}',
                    text: '{{ session('success-message') }}',
                    delay: 2000,
                    icon: 'icon-checkmark3',
                    type: 'success'
            });
        @endif
        @if (Session::has('success'))
            new PNotify({
                    title: '{{ trans('main.success') }}',
                    text: '{{ session('success') }}',
                    delay: 2000,
                    icon: 'icon-checkmark3',
                    type: 'success'
            });
        @endif
        @if (Session::has('warning-message'))

           new PNotify({
                title: '{{ trans('main.warning') }}',
                text: '{{ session('warning-message') }}',
                delay: 2000,
                icon: 'icon-warning22'
            });
        @endif
        @if (count($errors) > 0)
            @foreach ($errors->all() as $error)

                new PNotify({
                    title: '{{ trans('main.error') }}',
                    text: '{{ $error }}',
                    delay: 2000,
                    icon: 'icon-warning22',
                    type: 'error'
                });
            @endforeach
        @endif
        });
    </script>
@endif