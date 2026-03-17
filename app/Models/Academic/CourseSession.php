<?php
namespace App\Models\Academic;


use Illuminate\Database\Eloquent\Model;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\Room;

class CourseSession extends Model
{
    protected $table = 'course_session';

    protected $primaryKey = 'course_session_id';

    public $timestamps = true;

    protected $casts = [
        'course_instance_id' => 'integer',
        'room_id' => 'integer',
        'session_number' => 'integer',

        'session_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',

        'generated_from_schedule' => 'boolean',
        'created_at' => 'datetime'
    ];

    protected $fillable = [
        'course_instance_id',
        'session_date',
        'start_time',
        'end_time',
        'session_number',
        'room_id',
        'generated_from_schedule',
        'status'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function courseInstance()
    {
        return $this->belongsTo(CourseInstance::class, 'course_instance_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'course_session_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isCompleted()
    {
        return $this->status === 'Completed';
    }

    public function isCancelled()
    {
        return $this->status === 'Cancelled';
    }

    public function isScheduled()
    {
        return $this->status === 'Scheduled';
    }

}