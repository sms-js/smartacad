<?php

namespace App\Models\Admin\PinNumbers;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use Illuminate\Database\Eloquent\Model;

class ResultChecker extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'result_checkers';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'result_checker_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id', 'classroom_id', 'academic_term_id', 'pin_number_id'
    ];
    
    /**
     * An Result Checker Belongs To A Student
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(){
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * A Result Checker belongs to a Class Room
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classRoom(){
        return $this->belongsTo(ClassRoom::class, 'classroom_id');
    }

    /**
     * A Result Checker Belongs To An Academic Term
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicTerm(){
        return $this->belongsTo(AcademicTerm::class, 'academic_term_id');
    }
}
