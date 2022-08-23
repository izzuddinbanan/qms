@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        <div class="panel panel-flat" >
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-users"></i> @lang('main.handover_set')
                </h5>
            </div>

            <div class="panel-body">
                <div id="submit_field" class="transcation_field">
                    <div class="col-md-12">
                        <!-- <label>Handover Settings:</label> -->
                    </div>
                    <form action="{{ route('handover.editHandoverSetting') }}" method="POST">
                    @csrf
                    <table id="myTable" class="table table-bordered table-hover table-striped table-framed">
                        <thead>
                            <tr>
                                <td class="" >Handover Settings</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="row" style="margin-bottom:5px;">
                                        <div class="col-md-8">
                                            <p style="font-weight: bold;">{{$key->display_name ?? ''}}</p>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <label class="switch">
                                                <input type="checkbox" id="key" name="key"  checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            Checklist-type form.
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <a href="{{route('key.index')}}" id="btn_key" name="btn_key" type="submit" class="btn btn-primary">Manage <i class="icon-arrow-right14 position-right"></i></a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="row" style="margin-bottom:5px;">
                                        <div class="col-md-8">
                                            <p style="font-weight: bold;">{{$es->display_name}}</p>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <label class="switch">
                                                <input type="checkbox" id="es" name="es" onclick="es()" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            Checklist-type form.
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <a href="{{route('es.index')}}" id="btn_es" name="btn_es" type="submit" class="btn btn-primary">Manage <i class="icon-arrow-right14 position-right"></i></a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="row" style="margin-bottom:5px;">
                                        <div class="col-md-8">
                                            <p style="font-weight: bold;">{{$waiver->display_name}}</p>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <label class="switch">
                                                <input type="checkbox" id="waiver" name="waiver" onclick="waiver()" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            Text editor for content and a signature area.
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <a href="{{route('waiver.index')}}" id="btn_waiver" name="btn_waiver" type="submit" class="btn btn-primary">Manage <i class="icon-arrow-right14 position-right"></i></a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="row" style="margin-bottom:5px;">
                                        <div class="col-md-8">
                                            <p style="font-weight: bold;">{{$photo->display_name}}</p>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <label class="switch">
                                                <input type="checkbox" id="photo" name="photo" onclick="photo()" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            Allows for multiple photos to be taken.
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <a href="{{route('photo.index')}}" id="btn_photo" name="btn_photo" type="submit" class="btn btn-primary">Manage <i class="icon-arrow-right14 position-right"></i></a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="row" style="margin-bottom:5px;">
                                        <div class="col-md-8">
                                            <p style="font-weight: bold;">{{$acceptance->display_name}}</p>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <label class="switch">
                                                <input type="checkbox" id="acceptance" name="acceptance" onclick="acceptance()" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            Contains a Remarks section, Terms & Conditions and two signature areas.
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <a href="{{route('acceptance.index')}}" id="btn_acceptance" name="btn_acceptance" type="submit" class="btn btn-primary">Manage <i class="icon-arrow-right14 position-right"></i></a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="row" style="margin-bottom:5px;">
                                        <div class="col-md-8">
                                            <p style="font-weight: bold;">{{$survey->display_name}}</p>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <label class="switch">
                                                <input type="checkbox" id="survey" name="survey" onclick="survey()" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            Survey-type form.
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <a href="{{route('survey.index')}}" id="btn_survey" name="btn_survey" type="submit" class="btn btn-primary">Manage <i class="icon-arrow-right14 position-right"></i></a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <td>
                                <button type="submit" class="btn btn-dark" style="width:100%; background-color:black; color:white;">Save</button>        
                            </td>
                        </tfoot>
                    </table>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            if("{{$key->show}}"=="yes")
            {
                document.getElementById("key").checked = true;
                document.getElementById("btn_key").style.display = "";
            }
            else{
                document.getElementById("key").checked = false;
                document.getElementById("btn_key").style.display = "none";  
            }

            if("{{$es->show}}"=="yes")
            {
                document.getElementById("es").checked = true;
                document.getElementById("btn_es").style.display = "";
            }
            else{
                document.getElementById("es").checked = false;
                document.getElementById("btn_es").style.display = "none";  
            }

            if("{{$waiver->show}}"=="yes")
            {
                document.getElementById("waiver").checked = true;
                document.getElementById("btn_waiver").style.display = "";
                // btn_survey.style.display = "";  
            }
            else{
                document.getElementById("waiver").checked = false;
                document.getElementById("btn_waiver").style.display = "none";  
            }

            if("{{$photo->show}}"=="yes")
            {
                document.getElementById("photo").checked = true;
                document.getElementById("btn_photo").style.display = ""; 
            }
            else{
                document.getElementById("photo").checked = false;
                document.getElementById("btn_photo").style.display = "none";  
            }

            if("{{$acceptance->show}}"=="yes")
            {
                document.getElementById("acceptance").checked = true;
                document.getElementById("btn_acceptance").style.display = "";
            }
            else{
                document.getElementById("acceptance").checked = false;
                document.getElementById("btn_acceptance").style.display = "none";  
            }

            if("{{$survey->show}}"=="yes")
            {
                document.getElementById("survey").checked = true;
                document.getElementById("btn_survey").style.display = "";
                // btn_survey.style.display = "";  
            }
            else{
                document.getElementById("survey").checked = false;
                document.getElementById("btn_survey").style.display = "none";  
            }
        });

        $("#key").click(function() {
            // Get the checkbox
            var checkBox = document.getElementById("key");
            // Get the output text
            var btn_key = document.getElementById("btn_key");

            // If the checkbox is checked, display the output text
            if (checkBox.checked == true){
                btn_key.style.display = "";
            } else {
                btn_key.style.display = "none";
            }
        });

        $("#es").click(function() {
          // Get the checkbox
          var checkBox = document.getElementById("es");
          // Get the output text
          var btn_es = document.getElementById("btn_es");

          // If the checkbox is checked, display the output text
          if (checkBox.checked == true){
            btn_es.style.display = "";
          } else {
            btn_es.style.display = "none";
          }
        });


        $("#waiver").click(function() {
          // Get the checkbox
          var checkBox = document.getElementById("waiver");
          // Get the output text
          var btn_waiver = document.getElementById("btn_waiver");

          // If the checkbox is checked, display the output text
          if (checkBox.checked == true){
            btn_waiver.style.display = "";
          } else {
            btn_waiver.style.display = "none";
          }
        });

        $("#photo").click(function() {
          // Get the checkbox
          var checkBox = document.getElementById("photo");
          // Get the output text
          var btn_photo = document.getElementById("btn_photo");

          // If the checkbox is checked, display the output text
          if (checkBox.checked == true){
            btn_photo.style.display = "";
          } else {
            btn_photo.style.display = "none";
          }
        });

        $("#acceptance").click(function() {
          // Get the checkbox
          var checkBox = document.getElementById("acceptance");
          // Get the output text
          var btn_acceptance = document.getElementById("btn_acceptance");

          // If the checkbox is checked, display the output text
          if (checkBox.checked == true){
            btn_acceptance.style.display = "";
          } else {
            btn_acceptance.style.display = "none";
          }
        });

        $("#survey").click(function() {
          // Get the checkbox
          var checkBox = document.getElementById("survey");
          // Get the output text
          var btn_survey = document.getElementById("btn_survey");

          // If the checkbox is checked, display the output text
          if (checkBox.checked == true){
            btn_survey.style.display = "";
          } else {
            btn_survey.style.display = "none";
          }
        });
    </script>
    
    <style>
        .switch {
          position: relative;
          display: inline-block;
          width: 60px;
          height: 34px;
        }

        .switch input { 
          opacity: 0;
          width: 0;
          height: 0;
        }

        .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #ccc;
          -webkit-transition: .4s;
          transition: .4s;
        }

        .slider:before {
          position: absolute;
          content: "";
          height: 26px;
          width: 26px;
          left: 4px;
          bottom: 4px;
          background-color: white;
          -webkit-transition: .4s;
          transition: .4s;
        }

        input:checked + .slider {
          background-color: #2196F3;
        }

        input:focus + .slider {
          box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
          -webkit-transform: translateX(26px);
          -ms-transform: translateX(26px);
          transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
          border-radius: 34px;
        }

        .slider.round:before {
          border-radius: 50%;
        }
    </style>
    
@endsection