<?php

namespace App\Http\Controllers\Front\Assessments;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\Accounts\Students\StudentClass;
use App\Models\Admin\Exams\Exam;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use stdClass;

class ExamsController extends Controller
{
    /**
     * Display a listing of the Menus for Master Records.
     *
     * @return Response
     */
    public function getIndex()
    {
        $academic_years = AcademicYear::lists('academic_year', 'academic_year_id')->prepend('Select Academic Year', '');
        return view('front.assessments.exams.index', compact('academic_years'));
    }

    /**
     * Search For Students in a classroom for an academic term
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSearchStudents(Request $request)
    {
        $inputs = $request->all();
        $response = array();
        $response['flag'] = 0;
        $output = [];
        $myStudents = Auth::user()->students()->get(['student_id'])->toArray();

        $students = (isset($inputs['academic_year_id']))
            ? StudentClass::where('academic_year_id', $inputs['academic_year_id'])->whereIn('student_id', $myStudents)->get() : [];

        if(count($students) > 0){
            foreach ($students as $student){
                $object = new stdClass();
                $object->student_id = $student->student_id;
                $object->hashed_stud = $this->getHashIds()->encode($student->student_id);
                $object->hashed_term = $this->getHashIds()->encode($inputs['academic_term_id']);
                $object->student_no = $student->student()->first()->student_no;
                $object->name = $student->student()->first()->fullNames();
                $object->gender = $student->student()->first()->gender;
                $output[] = $object;
            }
            //Sort The Students by name
            usort($output, function($a, $b)
            {
                return strcmp($a->name, $b->name);
            });
            $response['flag'] = 1;
            $response['Students'] = $output;
        }

        echo json_encode($response);
    }

    /**
     * Displays the details of the subjects students scores for a specific academic term
     * @param String $encodeStud
     * @param String $encodeTerm
     * @return \Illuminate\View\View
     */
    public function getTerminalResult($encodeStud, $encodeTerm)
    {
        $decodeStud = $this->getHashIds()->decode($encodeStud);
        $decodeTerm = $this->getHashIds()->decode($encodeTerm);
        $student = (empty($decodeStud)) ? abort(305) : Student::findOrFail($decodeStud[0]);
        $term = (empty($decodeTerm)) ? abort(305) : AcademicTerm::findOrFail($decodeTerm[0]);
        $classroom = $student->currentClass($term->academicYear->academic_year_id);

        $position = Exam::terminalClassPosition($term->academic_term_id, $classroom->classroom_id, $student->student_id);
        $position = (object) array_shift($position);
//        $subjects = $student->subjectClassRooms()->where('academic_term_id', $term->academic_term_id)->where('classroom_id', $class_id)->get();
        $subjects = SubjectClassRoom::where('academic_term_id', $term->academic_term_id)->where('classroom_id', $classroom->classroom_id)->get();

        return view('front.assessments.terminal.student', compact('student', 'subjects', 'term', 'position', 'classroom'));
    }

    /**
     * Displays a printable details of the subjects students scores for a specific academic term
     * @param String $encodeStud
     * @param String $encodeTerm
     * @return \Illuminate\View\View
     */
    public function getPrintTerminalResult($encodeStud, $encodeTerm)
    {
        $decodeStud = $this->getHashIds()->decode($encodeStud);
        $decodeTerm = $this->getHashIds()->decode($encodeTerm);
        $student = (empty($decodeStud)) ? abort(305) : Student::findOrFail($decodeStud[0]);
        $term = (empty($decodeTerm)) ? abort(305) : AcademicTerm::findOrFail($decodeTerm[0]);
        $classroom = $student->currentClass($term->academicYear->academic_year_id);

        $position = Exam::terminalClassPosition($term->academic_term_id, $classroom->classroom_id, $student->student_id);
        $position = (object) array_shift($position);
        $subjects = SubjectClassRoom::where('academic_term_id', $term->academic_term_id)->where('classroom_id', $classroom->classroom_id)->get();

        return view('front.assessments.terminal.print', compact('student', 'subjects', 'term', 'position', 'classroom'));
    }
}