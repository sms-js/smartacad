@extends('admin.layout.default')

@section('page-level-css')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Grades Grouping')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li><i class="fa fa-chevron-right"></i></li>
    <li>
        <a href="{{ url('/grades') }}">Grades Grouping</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page"> Grades</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-8 col-xs-12 col-md-offset-2 margin-bottom-10">
            <form method="post" action="/grades/class-groups" role="form" class="form-horizontal">
                {!! csrf_field() !!}
                <div class="form-group">
                    <label class="col-md-3 control-label">Class Groups</label>

                    <div class="col-md-6">
                        <div class="col-md-9">
                            <select class="form-control selectpicker" name="classgroup_id" id="classgroup_id">
                                @foreach($classgroups as $key => $value)
                                    @if($classgroup && $classgroup->classgroup_id === $key)
                                        <option selected value="{{$key}}">{{$value}}</option>
                                    @else
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary pull-right" type="submit">Filter</button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-10">
                        <h3 class="text-center">Grades Assigned in:
                            <span class="text-primary">{{ ($classgroup) ? $classgroup->classgroup : 'All' }}</span> Class Group</h3>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Grades Grouping Setup</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn btn-sm green add_grade"> Add New
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <form method="post" action="/grades" role="form" class="form">
                                {!! csrf_field() !!}
                                <div class="table-responsive">
                                <table class="table table-bordered table-striped table-actions" id="grade_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 1%;">s/no</th>
                                        <th style="width: 24%;">Grades</th>
                                        <th style="width: 20%;">Class Group</th>
                                        <th style="width: 13%;">Grades Abbr.</th>
                                        <th style="width: 13%;">Upper Bound</th>
                                        <th style="width: 13%;">Lower Bound</th>
                                        <th style="width: 8%;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th style="width: 1%;">s/no</th>
                                        <th style="width: 24%;">Grades</th>
                                        <th style="width: 20%;">Class Group</th>
                                        <th style="width: 13%;">Grades Abbr.</th>
                                        <th style="width: 13%;">Upper Bound</th>
                                        <th style="width: 13%;">Lower Bound</th>
                                        <th style="width: 8%;">Actions</th>
                                    </tr>
                                    </tfoot>
                                    @if(count($classgroups) > 1)
                                        @if(count($grades) > 0)
                                            <tbody>
                                            <?php $i = 1; ?>
                                            @foreach($grades as $grade)
                                                <tr>
                                                    <td class="text-center">{{$i++}} </td>
                                                    <td>
                                                        {!! Form::text('grade[]', $grade->grade, ['placeholder'=>'Grade', 'class'=>'form-control', 'required'=>'required']) !!}
                                                        {!! Form::hidden('grade_id[]', $grade->grade_id, ['class'=>'form-control']) !!}
                                                    </td>
                                                    <td>{!! Form::select('classgroup_id[]', $classgroups, $grade->classgroup_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                    <td>{!! Form::text('grade_abbr[]', $grade->grade_abbr, ['placeholder'=>'Grade Abbr.', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                    <td>{!! Form::text('upper_bound[]', $grade->upper_bound, ['placeholder'=>'Upper Bound', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                    <td>{!! Form::text('lower_bound[]', $grade->lower_bound, ['placeholder'=>'Lower Bound', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                    <td>
                                                        <button  data-name="{{$grade->grade}}"
                                                                 data-action="/grades/delete/{{$grade->grade_id}}"
                                                                 class="btn btn-danger btn-xs btn-condensed btn-sm confirm-delete-btn">
                                                            <span class="fa fa-trash-o"></span> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        @else
                                            <tr>
                                                <td class="text-center">1</td>
                                                <td>
                                                    {!! Form::text('grade[]', '', ['placeholder'=>'Grade', 'class'=>'form-control', 'required'=>'required']) !!}
                                                    {!! Form::hidden('grade_id[]', '-1', ['class'=>'form-control']) !!}
                                                </td>
                                                <td>{!! Form::select('classgroup_id[]', $classgroups, '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::text('grade_abbr[]', '', ['placeholder'=>'Grade Abbr.', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::text('upper_bound[]', '', ['placeholder'=>'Upper Bound', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::text('lower_bound[]', '', ['placeholder'=>'Lower Bound', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>
                                                    <button class="btn btn-danger btn-rounded btn-condensed btn-sm">
                                                        <span class="fa fa-times"></span> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @else
                                        <tr><td colspan="7" class="text-center"><label class="label label-danger"><strong>An Academic Years Record Must Be Inserted Before Inserting Grade</strong></label></td></tr>
                                    @endif
                                </table>
                                <div class="col-md-12 margin-bottom-10">
                                    <div class="btn-group pull-left">
                                        <button class="btn btn-sm green add_grade"> Add New
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-actions noborder">
                                    <button type="submit" class="btn blue pull-right">Submit</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END CONTENT BODY -->
    @endsection


@section('page-level-js')
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
@endsection

@section('layout-script')
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            $('.add_grade').click(function(e){
                e.preventDefault();
                var clone_row = $('#grade_table tbody tr:last-child').clone();

                $('#grade_table tbody').append(clone_row);

                clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
                clone_row.children(':nth-child(2)').children('input').val('');
                clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
                clone_row.children(':nth-child(3)').children('select').val('');
                clone_row.children(':nth-child(4)').children('input').val('');
                clone_row.children(':nth-child(5)').children('input').val('');
                clone_row.children(':nth-child(6)').children('input').val('');
                clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-xs remove_grade"><span class="fa fa-times"></span> Remove</button>');
            });

            $(document.body).on('click','.remove_grade',function(){
                $(this).parent().parent().remove();
            });

            setTabActive('[href="/grades"]');
            setTableData($('#grade_table')).init();
        });
    </script>
@endsection
