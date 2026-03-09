<?php


namespace App\Models\Student;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Student\Student;

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
		'student_id' => 'integer',
		'is_primary' => 'boolean'
	];

	protected $fillable = [
		'student_id',
		'phone_number',
		'is_primary'
	];

	public function student()
	{
		return $this->belongsTo(Student::class, 'student_id');
	}

    public function isPrimary()
    {
        return $this->is_primary === true;
    }

    public function formatted()
    {
        return preg_replace('/[^0-9]/', '', $this->phone_number);
    }

    public function isEgyptian()
    {
        return str_starts_with($this->formatted(), '01');
    }

    /*
    |--------------------------------------------------------------------------
    | Business Actions
    |--------------------------------------------------------------------------
    */

    public function setPrimary()
    {
        self::where('student_id', $this->student_id)
            ->update(['is_primary' => false]);

        $this->update([
            'is_primary' => true
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePrimary(Builder $query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeForStudent(Builder $query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /*
    |--------------------------------------------------------------------------
    | Utilities
    |--------------------------------------------------------------------------
    */

    public function whatsappFormat()
    {
        $phone = $this->formatted();

        if (str_starts_with($phone, '0')) {
            return '20' . substr($phone, 1);
        }

        return $phone;
    }
}
