<?php

namespace App\Models\Admin\MasterRecords\Classes;

use Illuminate\Database\Eloquent\Model;

class ClassGroup extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'classgroups';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'classgroup_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'classgroup',
        'ca_weight_point',
        'exam_weight_point',
    ];

    /**
     * A Class Group Has Many Class Level
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function classLevels(){
        return $this->hasMany('App\Models\Admin\MasterRecords\Classes\ClassLevel', 'classlevel_id');
    }
}
