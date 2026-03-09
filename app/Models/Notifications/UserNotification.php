<?php

namespace App\Models\Notifications;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use Illuminate\Database\Eloquent\Builder;



/**
 * Class UserNotification
 * 
 * @property int $user_notification_id
 * @property int $employee_id
 * @property string $title
 * @property string $message
 * @property string|null $related_entity_type
 * @property int|null $related_entity_id
 * @property bool|null $is_read
 * @property Carbon|null $created_at
 * 
 * @property Employee $employee
 *
 * @package App\Models
 */
class UserNotification extends Model
{
	protected $table = 'user_notification';
	protected $primaryKey = 'user_notification_id';
	public $timestamps = false;

	protected $casts = [
		'employee_id' => 'integer',
		'related_entity_id' => 'integer',
		'is_read' => 'boolean',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'employee_id',
		'title',
		'message',
		'related_entity_type',
		'related_entity_id',
		'is_read'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'employee_id');
	}

    public function isRead()
    {
        return $this->is_read === true;
    }

    public function isUnread()
    {
        return $this->is_read === false;
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true
        ]);
    }

    public function markAsUnread()
    {
        $this->update([
            'is_read' => false
        ]);
    }

    public function scopeUnread(Builder $query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead(Builder $query)
    {
        return $query->where('is_read', true);
    }

    public function scopeForEmployee(Builder $query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeLatest(Builder $query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function notificationAge()
    {
        if (!$this->created_at) {
            return null;
        }

        return $this->created_at->diffForHumans();
    }

}
