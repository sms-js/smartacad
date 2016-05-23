@extends('admin.layout.default')

@section('page-level-css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('layout-style')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/pages/css/profile-2.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection
@section('title', 'Modify Student')

@section('breadcrumb')
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-home"></i>
    </li>
    <li>
        <span>Modify Student</span>
    </li>
@stop

@section('content')
    <h3 class="page-title"> Modify Student Record</h3>

    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <!-- BEGIN SAMPLE FORM PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="icon-user font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase"> Modify Student</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    @include('errors.errors')
                        {!! Form::open([
                                'method'=>'POST',
                                'class'=>'form',
                                'role'=>'form'
                            ])
                        !!}
                        {!! Form::hidden('student_id', $student->student_id) !!}
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label">Status <span class="text-danger">*</span></label>
                                {!! Form::select('status_id', $status, $student->status_id, ['class'=>'form-control selectpicker', 'required'=>'required']) !!}
                            </div>
                            <div class="form-group">
                                <label class="control-label">Sponsor Name<span class="text-danger">*</span></label>
                                {!! Form::text('sponsor_name', $student->sponsor()->first()->fullNames(), ['placeholder'=>'Sponsor Name', 'class'=>'form-control', 'id'=>'sponsor_name', 'required'=>'required']) !!}
                                {!! Form::hidden('sponsor_id', $student->sponsor_id, ['class'=>'form-control', 'id'=>'sponsor_id']) !!}
                            </div>
                            <div class="form-group">
                                <label class="control-label">First Name <span class="text-danger">*</span></label>
                                {!! Form::text('first_name', $student->first_name, ['placeholder'=>'First Name', 'class'=>'form-control', 'required'=>'required']) !!}
                            </div>
                            <div class="form-group">
                                <label class="control-label">Last Name <span class="text-danger">*</span></label>
                                {!! Form::text('last_name', $student->last_name, ['placeholder'=>'Last Name', 'class'=>'form-control', 'required'=>'required']) !!}
                            </div>
                            <div class="form-group">
                                <label class="control-label">Middle Name</label>
                                {!! Form::text('middle_name', $student->middle_name, ['placeholder'=>'Middle Name', 'class'=>'form-control']) !!}
                            </div>
                            <div class="form-group">
                                <label class="control-label">Gender <span class="text-danger">*</span></label>
                                {!! Form::select('gender', [''=>'Gender', 'Male'=>'Male', 'Female'=>'Female'], $student->gender, ['class'=>'form-control selectpicker', 'required'=>'required']) !!}
                            </div>
{{--                            {{dd($classroom)}}--}}
                            <div class="form-group">
                                <label class="control-label">Class Level <span class="text-danger">*</span></label>
                                <div>
                                    @if($classroom === null)
                                        {!! Form::select('classlevel_id', $classlevels, '', ['class'=>'form-control', 'id'=>'classlevel_id']) !!}
                                    @else
                                        {!! Form::select('classlevel_id', $classlevels, $classroom->classlevel_id, ['class'=>'form-control', 'id'=>'classlevel_id']) !!}
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Class Room <span class="text-danger">*</span></label>
                                <div>
                                    @if($classroom == null)
                                        {!! Form::select('classroom_id', [''=>'Select Class Room'], '', ['class'=>'form-control', 'id'=>'classroom_id']) !!}
                                    @else
                                        {!! Form::select('classroom_id', $classrooms, $classroom->classroom_id, ['class'=>'form-control', 'id'=>'classroom_id']) !!}
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Date Of Birth </label>
                                <input class="form-control date-picker" data-date-format="yyyy-mm-dd" name="dob" type="text" value="{!! ($student->dob) ?  $student->dob->format('Y-m-d') : '' !!}" />
                            </div>
                            <div class="form-group">
                                <label class="control-label">State </label>
                                <div>
                                    @if($lga === null)
                                        {!! Form::select('state_id', $states, '', ['class'=>'form-control', 'id'=>'state_id']) !!}
                                    @else
                                        {!! Form::select('state_id', $states, $lga->state_id, ['class'=>'form-control', 'id'=>'state_id']) !!}
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">L.G.A </label>
                                <div>
                                    @if($lga == null)
                                        {!! Form::select('lga_id', [''=>'Nothing Selected'], '', ['class'=>'form-control', 'id'=>'lga_id']) !!}
                                    @else
                                        {!! Form::select('lga_id', $lgas, $lga->lga_id, ['class'=>'form-control', 'id'=>'lga_id']) !!}
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Contact Address</label>
                                <textarea class="form-control" rows="3" required placeholder="Contact Address" name="address">{{ $student->address }}</textarea>
                            </div>
                            <div class="margiv-top-10">
                                <button class="btn green pull-right btn-lg"> Update Info </button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </form>
                </div>
            </div>
            <!-- END SAMPLE FORM PORTLET-->
        </div>
    </div>

@endsection

@section('page-level-js')
    <script src="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.js') }}" type="text/javascript"></script>
@endsection

@section('layout-script')
    <script src="{{ asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/accounts/students.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/students"]');
        });
    </script>
@endsection