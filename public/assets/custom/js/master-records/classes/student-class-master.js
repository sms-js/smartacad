/**
 * Created by Cecilee2 on 8/4/2015.
 */

jQuery(document).ready(function() {
    var tutors = $('#tutors').clone();
    var old_btn;

    // Ajax Get Class Rooms Based on the Class Level
    getDependentListBox($('#student_classlevel_id'), $('#student_classroom_id'), '/list-box/classroom/');
    getDependentListBox($('#view_classlevel_id'), $('#view_classroom_id'), '/list-box/classroom/');

    //When The Checkbox is Checked To Assign A Student to Class
    $(document.body).on('click', '.assign_student', function(){
        var class_id = $('#hidden_classroom_id').val();
        var year_id = $('#hidden_academic_year_id').val();
        var student_id = $(this).val();
        var student_class_id = $(this).prop('title');
        var parent_tr = $(this).parent().parent();
        //Assign A Students
        if($(this).prop('checked') === true){
            $(this).attr("checked", "checked");
            $.post('/class-rooms/assign', {student_class_id:student_class_id, student_id:student_id, class_id:class_id, year_id:year_id}, function(data){
                if(data !== '0'){
                    var title = parent_tr.children().next().next();
                    title.children().attr("title", data);
                    if($("#assign_student_tr").next().children().html() === "No Student Has Been Assigned"){
                        $("#assign_student_tr").next().remove();
                    }
                    $("#assign_student_tr").after("<tr><td>"+parent_tr.children().html()+"</td><td>"+parent_tr.children().next().html()+"</td><td>"+title.html()+"</td></tr>");
                    parent_tr.remove();
                }
            });
            //Remove An Assigned Examiner
        } else if($(this).prop('checked') === false){
            $(this).removeAttr("checked");
            $.post('/class-rooms/assign', {student_class_id:student_class_id, student_id:student_id}, function(data){
                if(data !== '0'){
                    var title = parent_tr.children().next().next();
                    title.children().attr("title", '-1');
                    if($("#available_student_tr").next().children().html() === "No Student Available"){
                        $("#available_student_tr").next().remove();
                    }
                    $("#available_student_tr").after("<tr><td>"+parent_tr.children().html()+"</td><td>"+parent_tr.children().next().html()+"</td><td>"+title.html()+"</td></tr>");
                    parent_tr.remove();
                }
            });
        }
        //return false;
    });

    //When the edit button is clicked show Tutors Drop Down
    $(document.body).on('click', '.edit-class-master', function(){
        var buttonTD = $(this).parent();
        tutors.removeClass('hide');
        var employees = tutors.clone();
        old_btn = $(this).clone();
        buttonTD.html(employees);
        employees.val($(this).attr('rel'));
        employees.prop('id', '');
        employees.attr('rel', $(this).val());
        employees.attr('title', $(this).attr('title'));
        employees.addClass('class-master-select');
        buttonTD.children('select').focus();
    });

    //When No Changes is made to the Teachers Listbox //On Blur
    $(document.body).on('blur', '.class-master-select', function(){
        var td = $(this).parent();
        td.html(old_btn);
    });

    //On Change of the employees name assign to the class
    $(document.body).on('change', '.class-master-select', function(){
        var class_master_id = $(this).attr('rel');
        var classroom_id = $(this).attr('title');
        var year_id = $('#hidden_master_year_id').val();
        var buttonTD = $(this).parent();
        var user_id = $(this).val();
        var name = $(this).children('option:selected').text();

        $.ajax({
            type: "POST",
            data: {class_master_id:class_master_id, classroom_id:classroom_id, year_id:year_id, user_id:user_id},
            url: '/class-rooms/assign-class-masters',
            success: function (data) {
                buttonTD.html('<button value="'+data+'" title="'+classroom_id+'" rel="'+user_id+'" class="btn btn-link edit-class-master">\n\
                <i class="fa fa-edit"></i> '+name+'</button></td>');
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                set_msg_box($('#msg_box2'), 'Error...Kindly Try Again', 2)
            }
        });
    });
});

var UIBlockUI = function() {

    var handleSample1 = function() {

        //When the search button is clicked
        $(document.body).on('submit', '#assign_student_form', function(){
            var values = $(this).serialize();
            $('#hidden_classroom_id').val($('#student_classroom_id').val());
            $('#hidden_academic_year_id').val($('#student_academic_year_id').val());

            App.blockUI({
                target: '#assign2student',
                animate: true
            });

            $.post('/class-rooms/search-students', values, function(data){
                try{
                    var obj = $.parseJSON(data);
                    var available = '<caption><strong>List Of Available Students</strong></caption>\
                                    <tr id="available_student_tr">\
                                        <th >Student No.</th>\
                                        <th >Full Name</th>\
                                        <th ><i class="fa fa-check-square"></i> </th>\
                                    </tr>';
                    var assign = '<caption><strong>List Of Assigned Students</strong></caption>\
                                    <tr id="assign_student_tr">\
                                        <th >Student No.</th>\
                                        <th >Full Name</th>\
                                        <th ><i class="fa fa-times"></i> </th>\
                                    </tr>';

                    if(obj.flag2 == 1){
                        $.each(obj.StudentsNoClass, function(key, value) {
                            available += '<tr>\
                                <td>'+value.student_no+'</td>\n\
                                <td>'+value.name+'</td>\n\
                                <td><input type="checkbox" class="assign_student" title="-1" value="'+value.student_id+'"/> </td></tr>\n\
                            ';
                        });
                        $('#available_students').html(available);
                    }else if(obj.Flag2 === 0){
                        available += '<tr><th colspan="3">No Student Available</th></tr>';
                        $('#available_students').html(available);
                    }
                    if(obj.flag1 == 1){
                        $.each(obj.StudentsClass, function(key, value) {
                            assign += '<tr>\
                                <td>'+value.student_no+'</td>\n\
                                <td>'+value.name+'</td>\n\
                                <td><input type="checkbox" class="assign_student" title="'+value.student_class_id+'" checked="checked" value="'+value.student_id+'"/> </td></tr>\n\
                            ';
                        });
                        $('#assigned_students').html(assign);
                    }else if(obj.Flag === 0){
                        assign += '<tr><th colspan="3">No Student Has Been Assigned</th></tr>';
                        $('#assigned_students').html(assign);
                    }

                    window.setTimeout(function() {
                        App.unblockUI('#assign2student');
                    }, 2000);
                    $('#assign_student_table').removeClass('hide');
                    //Scroll To Div
                    scroll2Div($('#assign_student_table'));

                } catch (exception) {
                    $('#assigned_students').html(data);
                    set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2);
                    App.unblockUI('#assign2student');
                }
            });
            return false;
        });

        //When the search button is clicked for viewing Students
        $(document.body).on('submit', '#search_student_form', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#search4student',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/class-rooms/view-students',
                data: values,
                success: function (data) {
                    //console.log(data);

                    var obj = $.parseJSON(data);
                    var assign = '<thead>\
                                    <tr>\
                                        <th>#</th>\
                                        <th>Student No.</th>\
                                        <th>Student Name</th>\
                                        <th>Gender</th>\
                                        <th>Sponsor</th>\
                                        <th>Class Room</th>\
                                        <th>View</th>\
                                    </tr>\
                                </thead>\
                                <tbody>';
                    if(obj.flag === 1){
                        $.each(obj.Students, function(key, value) {
                            assign += '<tr>' +
                                '<td>'+(key + 1)+'</td>' +
                                '<td>'+value.student_no+'</td>' +
                                '<td>'+value.name+'</td>' +
                                '<td>'+value.gender+'</td>' +
                                '<td><a href="/sponsors/view/'+value.sponsor_id+'" class="btn btn-link"><i class="fa fa-user"></i> '+value.sponsor+'</a></td>' +
                                '<td>'+value.classroom+'</td>' +
                                '<td><a href="/students/view/'+value.student_id+'" class="btn btn-link"><i class="fa fa-eye"></i> Proceed</a></td>' +
                            '</tr>';
                        });
                    }
                    assign += '</tbody>';

                    $('#view_students_datatable').html(assign);
                    //FormEditable.init();
                    setTableData($('#view_students_datatable')).refresh();
                    setTableData($('#view_students_datatable')).init();

                    window.setTimeout(function() {
                        App.unblockUI('#search4student');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#view_students_datatable'));
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box1'), 'Error...Kindly Try Again', 2);
                    App.unblockUI('#search4student');
                }
            });
            return false;
        });

        //When the search button is clicked for Assigning Class Master
        $(document.body).on('submit', '#search_class_master_form', function(){
            var values = $(this).serialize();
            $('#hidden_master_year_id').val($('#academic_year_id').val());

            App.blockUI({
                target: '#assign_classMaster',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/class-rooms/class-masters',
                data: values,
                success: function (data) {
                    //console.log(data);

                    var obj = $.parseJSON(data);
                    var assign = '<thead>\
                                    <tr>\
                                        <th>#</th>\
                                        <th>Class Room</th>\
                                        <th>No. of Student</th>\
                                        <th>Class Master</th>\
                                    </tr>\
                                </thead>\
                                <tbody>';
                    if(obj.flag === 1){
                        $.each(obj.ClassRooms, function(key, value) {
                            assign += '<tr>' +
                                '<td>'+(key + 1)+'</td>' +
                                '<td>'+value.classroom+'</td>' +
                                '<td>'+value.students+'</td>' +
                                '<td><button class="btn btn-link edit-class-master" value="'+value.class_master_id+'" rel="'+value.user_id+'" title="'+value.classroom_id+'"><i class="fa fa-edit"></i> '+value.name+'</button></td>' +
                                '</tr>';
                        });
                    }
                    assign += '</tbody>';

                    $('#class_master_datatable').html(assign);

                    window.setTimeout(function() {
                        App.unblockUI('#assign_classMaster');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#class_master_datatable'));
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box2'), 'Error...Kindly Try Again', 2);
                    App.unblockUI('#assign_classMaster');
                }
            });
            return false;
        });
    };

    return {
        //main function to initiate the module
        init: function() {

            handleSample1();
        }
    };
}();

jQuery(document).ready(function() {
    UIBlockUI.init();

});