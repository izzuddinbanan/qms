@extends('layouts.template2')

@section('main')
    <div class="content">
        <div class="panel panel-flat" >
            @include('components.body.panel-head', [
                'title'     => trans('main.customer'),
                'desc'      => trans('main.add'),
                'icon'      => 'icon-users',
            ])

            <div class="panel-body">
                <div class="row">
                    
                    <form action="{{ route('customer.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="text-semibold">@lang('main.email') :</label>
                                <input type="text" class="form-control" name="email" id="email" value="{{ old('email') }}" autocomplete="off" autofocus="" required="" placeholder="email" autocomplete="">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="text-semibold">Mr/Mrs :</label>
                                <select data-placeholder="Please Select" class="select-search" name="salutation" id="salutation" required="">
                                    <option value="">Please Select</option>
                                    <option value="mr" {{ old('salutation') == 'mr' ? 'selected' : '' }}>Mr</option>
                                    <option value="Mrs" {{ old('salutation') == 'mrs' ? 'selected' : '' }}>Mrs</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="text-semibold">@lang('main.name') :</label>
                                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" autocomplete="off" autofocus="" required="">
                            </div>
                        </div>


                        <div class="clearfix"></div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-semibold">IC No :</label>
                                <input type="text" class="form-control" name="ic_no" id="ic_no" value="{{ old('ic_no') }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-semibold">Passport No :</label>
                                <input type="text" class="form-control" name="passport_no" id="passport_no" value="{{ old('passport_no') }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-semibold">Company Reg No :</label>
                                <input type="text" class="form-control" name="company_reg_no" id="company_reg_no" value="{{ old('company_reg_no') }}" autocomplete="off">
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-semibold">Phone No :</label>
                                <input type="text" class="form-control" name="phone_no" id="phone_no" value="{{ old('phone_no') }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-semibold">House No :</label>
                                <input type="text" class="form-control" name="house_no" id="house_no" value="{{ old('house_no') }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-semibold">Office No :</label>
                                <input type="text" class="form-control" name="office_no" id="office_no" value="{{ old('house_no') }}" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="text-semibold">Mailing Address :</label>
                                <textarea class="form-control" name="mail_address" id="mail_address">{{ old('mail_address') }}</textarea>
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-semibold">Category :</label>
                                <select data-placeholder="Please Select" class="select-search" name="category_customer" id="category_customer" required="">
                                    <option value="">Please Select</option>
                                    <option value="primary">Primary</option>
                                    <option value="joint">Joint</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-semibold">Unit :</label>
                                <select data-placeholder="Please Select" class="select-search" name="unit" id="unit" required="">
                                    <option value="">Please Select</option>
                                </select>
                            </div>
                        </div>


                        <div class="clearfix"></div>
                        
                        @include('components.forms.basic-button', [
                            'route'     => route('customer.index'),
                            'div_size'  => 'col-md-12',
                            'position'  => 'right',
                        ])

                    </form>
                </div>

            </div>
        </div>
    </div>
    

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">
        var primary = {!! json_encode($primary) !!}
        var joint = {!! json_encode($joint) !!}


        $(document).ready(function(){

            var email_user = {!! json_encode($email_user) !!};

            var availableUser = [];
            email_user.forEach(element=> {
                availableUser.push(element);
            });

            $( "#email" ).autocomplete({
                source: availableUser
            });

            $("#category_customer").change(function(){

                var unit = [];
                switch($(this).val()) {

                    case 'primary':
                        unit = primary;
                        break;

                    case 'joint':
                        unit = joint;
                        break;
                }

                $('#unit').empty();
                unit.forEach(element => {
                    $('#unit').append('<option value="'+ element["id"] +'" >'+ element["name"] +'</option>');
                });
            });


            $("#email").on('keyup',function(){

                $.ajax({
                    url: '{{ route("customer.check-customer") }}',
                    type:'POST',
                    data: {'email' : $(this).val()},
                    success:function(response){
                        
                        if(response["status"] == 'success') {

                            $("#salutation").val(response["data"]["salutation"]).trigger('change');
                            $("#name").val(response["data"]["name"]);
                            $("#ic_no").val(response["data"]["ic_no"]);
                            $("#passport_no").val(response["data"]["passport_no"]);
                            $("#company_reg_no").val(response["data"]["comp_reg_no"]);
                            $("#phone_no").val(response["data"]["phone_no"]);
                            $("#house_no").val(response["data"]["house_no"]);
                            $("#office_no").val(response["data"]["office_no"]);
                            $("#mail_address").val(response["data"]["mailing_address"]);
                        }

                    },  
                });
            });
        });

    </script>

    
@endsection