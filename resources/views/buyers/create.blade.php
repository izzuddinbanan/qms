@extends('layouts.template2')

@section('main')
    <div class="content">
        <div class="panel panel-flat" >
            @include('components.body.panel-head', [
                'title'     => 'Buyer',
                'desc'      => trans('main.add'),
                'icon'      => 'icon-users',
            ])

            <div class="panel-body">
                <div class="row">
                    
                    <form action="{{ route('buyer.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <legend>Buyer Information</legend>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="text-semibold">Buyer ID :</label>
                                <input type="text" class="form-control" name="buyer_id" id="buyer_id" value="{{ old('buyer_id') }}" autocomplete="off" autofocus="" placeholder="" autocomplete="">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="text-semibold">@lang('main.email') :</label>
                                <input type="text" class="form-control" name="email" id="email" value="{{ old('email') }}" autocomplete="off" autofocus="" required="" placeholder="" autocomplete="">
                            </div>
                        </div>

                        <div class="col-md-5">
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


                        <legend>Unit Information</legend>


                        <div class="unit-information">
                            <div class="unit-field">
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-semibold">Category :</label>
                                        <select data-placeholder="Please Select" class="select-search owner-category" name="owner_category[]" required="" onchange="listProject(this)">
                                            <option value="">Please Select</option>
                                            <option value="primary">Primary</option>
                                            <option value="joint">Joint</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-semibold">Project :</label>
                                        <select data-placeholder="Please Select" class="select-search list-project" name="owner_project[]" required="" onchange="listUnit(this)">
                                            <option value="">Please Select</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-semibold">Unit :</label>
                                        <select data-placeholder="Please Select" class="select-search list-unit" name="owner_unit[]" required="" onchange="filterUnit(this)">
                                            <option value="">Please Select</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <button class="btn btn-primary" id="addMore" type="button">Add More</button>
                        </div>



                        
                        @include('components.forms.basic-button', [
                            'route'     => route('buyer.index'),
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
        
        var unit_information = '<div class="unit-field">'+
                                '<div class="col-md-1">'+
                                    '<div class="form-group">'+
                                        '<label class="text-semibold" style="color: white">label</label>'+
                                        '<button class="btn-danger form-control delete-field" type="button" onclick="deleteField(this)"><i class="fa fa-trash"></i></button>'+
                                    '</div>'+
                                '</div>'+
                            
                            '<div class="col-md-3">'+
                                '<div class="form-group">'+
                                    '<label class="text-semibold">Category :</label>'+
                                    '<select data-placeholder="Please Select" class="select-search owner-category" name="owner_category[]" required="" onchange="listProject(this)">'+
                                        '<option value="">Please Select</option>'+
                                        '<option value="primary">Primary</option>'+
                                        '<option value="joint">Joint</option>'+
                                    '</select>'+
                                '</div>'+
                            '</div>'+

                            '<div class="col-md-4">'+
                                '<div class="form-group">'+
                                    '<label class="text-semibold">Project :</label>'+
                                    '<select data-placeholder="Please Select" class="select-search list-project" name="owner_project[]" required="" onchange="listUnit(this)">'+
                                        '<option value="">Please Select</option>'+
                                    '</select>'+
                                '</div>'+
                            '</div>'+

                            '<div class="col-md-4">'+
                                '<div class="form-group">'+
                                    '<label class="text-semibold">Unit :</label>'+
                                    '<select data-placeholder="Please Select" class="select-search list-unit" name="owner_unit[]" required="" onchange="filterUnit(this)">'+
                                        '<option value="">Please Select</option>'+
                                    '</select>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

    </script>

    <script type="text/javascript">
        var primary_project = {!! json_encode($primary_project) !!}
        var joint_project = {!! json_encode($joint_project) !!}

        var unit_selected = [];

        var pleaseSelect = '<option value="" >Please Select</option>';


        function listProject(element) {

            var indexClass = $(".owner-category").index(element);
            $($('.list-project').get(indexClass)).empty();
            $($('.list-unit').get(indexClass)).empty();

            var project= [];
            switch($(element).val()) {

                case 'primary':
                    project = primary_project;
                    break;

                case 'joint':
                    project = joint_project;
                    break;
            }

            $($('.list-project').get(indexClass)).append(pleaseSelect);
            project.forEach(element => {
                $($('.list-project').get(indexClass)).append('<option value="'+ element["id"] +'" >'+ element["name"] +'</option>');

            });

        }

        function listUnit(element){

            var indexClass = $(".list-project").index(element);

            var category = $($('.owner-category').get(indexClass)).val();
            $($('.list-unit').get(indexClass)).empty();

            switch(category) {

                case 'primary':
                    project = primary_project;
                    break;

                case 'joint':
                    project = joint_project;
                    break;
            }

            $($('.list-unit').get(indexClass)).append(pleaseSelect);
            project.forEach(projects => {
                projects['drawing_set'].forEach(drawingSet => {
                    
                    switch(category) {

                        case 'primary':
                            drawingPlan  = drawingSet['drawing_plan_unit_no_owner'];
                            break;

                        case 'joint':
                            drawingPlan  = drawingSet['drawing_plan_unit_has_owner'];
                            break;
                    }
                    
                    drawingPlan.forEach(drawingPlan => {
                            // if(!unit_selected.includes(drawingPlan["id"].toString())){
                                var block = drawingPlan['block'] ? drawingPlan['block'] + '-' : '';
                                var level = drawingPlan['level'] ? drawingPlan['level'] + '-' : '';
                                var unit = drawingPlan['unit'] ? drawingPlan['unit'] : '';
                                var unitName = block + level + unit;
                                $($('.list-unit').get(indexClass)).append('<option value="'+ drawingPlan["id"] +'" >'+ unitName +'</option>');
                            // }
                        
                    });
                });

            });
        }


        function filterUnit(element){

            // var indexClass = $(".list-project").index(element);
            // unit_selected = [];
            // $('.list-unit').each(function(){
                
            //     var unit = $(this).val();
            //     unit_selected.push(unit)

            // })
            // console.log(unit_selected)
        }

        function deleteField(element){
            var indexClass = $(".delete-field").index(element);

            $($('.unit-field').get(indexClass + 1)).remove();
        }
        $(document).ready(function(){

            var email_user = {!! json_encode($email_user) !!};

            var availableUser = [];
            email_user.forEach(element=> {
                availableUser.push(element);
            });

            $( "#email" ).autocomplete({
                source: availableUser
            });

            // $("#category_customer").change(function(){

            //     var unit = [];
            //     switch($(this).val()) {

            //         case 'primary':
            //             unit = primary;
            //             break;

            //         case 'joint':
            //             unit = joint;
            //             break;
            //     }

            //     $('#unit').empty();
            //     unit.forEach(element => {
            //         $('#unit').append('<option value="'+ element["id"] +'" >'+ element["name"] +'</option>');
            //     });
            // });


            $(".list-project").on('change', function(){
                    
                
            })


            $("#email").on('keyup',function(){

                $.ajax({
                    url: '{{ route("customer.check-customer") }}',
                    type:'POST',
                    data: {'email' : $(this).val()},
                    success:function(response){
                        
                        if(response["status"] == 'success') {

                            $("#buyer_id").val(response["data"]["buyer_id"]);
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

            $('#addMore').click(function(){

                $('.unit-information').append(unit_information);
                $('.select-search').select2();
            });
        });

    </script>

    
@endsection