@extends('admin.layout.default')

@section('page-level-css')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('layout-style')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css"/>

<link href="{{ asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Edit Profile')

@section('breadcrumb')
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-home"></i>
    </li>
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <span>Edit Profile</span>
    </li>
@stop


@section('content')
    <h3 class="page-title">Profile | Edit Record</h3>

    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        @include('admin.layout.partials.profile-nav', ['active' => 'edit'])

        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-10">
                    <div class="portlet light ">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="icon-globe theme-font hide"></i>
                                <span class="caption-subject font-blue-madison bold uppercase">Update Profile Form</span>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <div class="form-body">
                                <div class="row profile-account">
                                    <div class="col-md-4">
                                        <ul class="ver-inline-menu tabbable margin-bottom-10">
                                            @if($user)
                                                <li class="{{ (session('active') == 'info') ? 'active' : '' }} {{ (!session()->has('active')) ? 'active' : '' }}">
                                                    <a data-toggle="tab" href="#info">
                                                        <i class="fa fa-cog"></i> Personal info </a>
                                                    <span class="after"> </span>
                                                </li>
                                            @endif
                                            <li class="{{ (session('active') == 'avatar') ? 'active' : '' }}">
                                                <a data-toggle="tab" href="#avatar"> <i class="fa fa-picture-o"></i> Change Avatar </a>
                                            </li>
                                            <li class="{{ (session('active') == 'password') ? 'active' : '' }}">
                                                <a data-toggle="tab" href="#password"> <i class="fa fa-lock"></i> Change Password </a>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col-md-8">
                                        @include('errors.errors')
                                        <div class="tab-content">
                                            @if($user)
                                                <div id="info" class="tab-pane {{ (session('active') == 'info') ? 'active' : '' }} {{ (!session()->has('active')) ? 'active' : '' }}">
                                                    <form action="/profiles/edit" role="form" method="post" class="form">
                                                        {{ csrf_field() }}
                                                        <div class="form-group">
                                                            <label class="control-label">Title</label>
                                                            @if($user->salutation_id === null)
                                                                {!! Form::select('salutation_id', $salutations, old('salutation_id'), ['class'=>'form-control input-lg selectpicker', 'required'=>'required']) !!}
                                                            @else
                                                                {!! Form::select('salutation_id', $salutations, $user->salutation_id, ['class'=>'form-control input-lg selectpicker', 'required'=>'required']) !!}
                                                            @endif
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">First Name</label>
                                                            {!! Form::text('first_name', $user->first_name, ['placeholder'=>'First Name', 'class'=>'form-control', 'required'=>'required']) !!}
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">Last Name</label>
                                                            {!! Form::text('last_name', $user->last_name, ['placeholder'=>'Last Name', 'class'=>'form-control', 'required'=>'required']) !!}
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">Middle Name</label>
                                                            {!! Form::text('middle_name', $user->middle_name, ['placeholder'=>'Middle Name', 'class'=>'form-control']) !!}
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">Email</label>
                                                            {!! Form::text('email', $user->email, ['placeholder'=>'Email', 'class'=>'form-control', 'required'=>'required']) !!}
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">Mobile Number</label>
                                                            {!! Form::text('phone_no', $user->phone_no, ['placeholder'=>'Mobile No', 'class'=>'form-control', 'required'=>'required']) !!}
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">Mobile Number 2</label>
                                                            {!! Form::text('phone_no2', $user->phone_no2, ['placeholder'=>'Mobile No 2', 'class'=>'form-control']) !!}
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">Gender</label>
                                                            {!! Form::select('gender', [''=>'Gender', 'Male'=>'Male', 'Female'=>'Female'], $user->gender, ['class'=>'form-control selectpicker', 'required'=>'required']) !!}
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">Date Of Birth </label>
                                                            <input class="form-control date-picker" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" name="dob" type="text"
                                                                   id="dob" value="{!! ($user->dob) ?  $user->dob->format('Y-m-d') : old('dob') !!}" required/>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">State </label>

                                                            <div>
                                                                @if(is_null($lga))
                                                                    {!! Form::select('state_id', $states, old('state_id'), ['class'=>'form-control', 'id'=>'state_id']) !!}
                                                                @else
                                                                    {!! Form::select('state_id', $states, $lga->state_id, ['class'=>'form-control', 'id'=>'state_id']) !!}
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">L.G.A </label>

                                                            <div>
                                                                @if(is_null($lga))
                                                                    {!! Form::select('lga_id', [''=>'Nothing Selected'], '', ['class'=>'form-control', 'id'=>'lga_id']) !!}
                                                                @else
                                                                    {!! Form::select('lga_id', $lgas, $lga->lga_id, ['class'=>'form-control', 'id'=>'lga_id']) !!}
                                                                @endif
                                                            </div>
                                                        </div>
                                                        {{--<div class="form-group">--}}
                                                        {{--<label>Contact Address</label>--}}
                                                        {{--<textarea class="form-control input-lg" rows="3" required placeholder="Contact Address" name="address">{{ $user->address }}</textarea>--}}
                                                        {{--</div>--}}
                                                        <div class="margiv-top-10">
                                                            <button class="btn green"> Update Info</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                            <div id="avatar" class="tab-pane {{ (session('active') == 'avatar') ? 'active' : '' }}">
                                                <form action="/profiles/avatar" role="form" method="post" enctype="multipart/form-data">
                                                    <div class="form-group">
                                                        {{ csrf_field() }}
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                                @if($user)
                                                                    <img src="{{ $user->getAvatarPath() }}" class="img-responsive pic-bordered" alt="{{ $user->fullNames() }}"/>
                                                                @else
                                                                    <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt=""/>
                                                                @endif
                                                            </div>
                                                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                                                            <div>
                                                                <span class="btn default btn-file">
                                                                <span class="fileinput-new"> Select image </span>
                                                                <span class="fileinput-exists"> Change </span>
                                                                <input type="file" name="avatar"></span>
                                                                <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="margin-top-10">
                                                        <button type="submit" class="btn green"> Upload</button>
                                                    </div>
                                                </form>
                                            </div>
                                            <div id="password" class="tab-pane {{ (session('active') == 'password') ? 'active' : '' }}">
                                                <form method="POST" action="/profiles/change-password" accept-charset="UTF-8" role="form">
                                                    {!! csrf_field() !!}
                                                    <input type="hidden" value="{{ $hashIds->encode($user->user_id) }}" name="id">

                                                    <div class="form-group">
                                                        <label class="control-label">Current Password</label>
                                                        <input name="password" type="password" class="form-control" required/></div>
                                                    <div class="form-group">
                                                        <label class="control-label">New Password</label>
                                                        <input name="new_password" type="password" class="form-control" required/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Re-type New Password</label>
                                                        <input name="password_confirmation" type="password" class="form-control" required/></div>
                                                    <div class="margin-top-10">
                                                        <button class="btn green btn-sm"> Change Password</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PROFILE CONTENT -->
    </div>

@endsection

@section('page-level-js')
    <script src="{{ asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.js') }}" type="text/javascript"></script>
@endsection

@section('layout-script')
    <script src="{{ asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/profiles/edit"]');

            $('#dob').datepicker();

            getDependentListBox($('#state_id'), $('#lga_id'), '/list-box/lga/');
        });
    </script>
@endsection



