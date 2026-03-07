<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class StudentPhone
 * 
 * @property int $phone_id
 * @property int $student_id
 * @property string $phone_number
 * @property bool $is_primary
 * 
 * @property Student $student
 *
 * @package App\Models
 */
class StudentPhone extends Model
{
	protected $table = 'student_phone';
	protected $primaryKey = 'phone_id';
	public $timestamps = false;

	protected $casts = [
		'student_id' => 'int',
		'is_primary' => 'bool'
	];

	protected $fillable = [
		'student_id',
		'phone_number',
		'is_primary'
	];

	public function student()
	{
		return $this->belongsTo(Student::class);
	}
}
