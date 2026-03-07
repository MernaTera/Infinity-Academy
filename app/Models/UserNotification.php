<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
		'employee_id' => 'int',
		'related_entity_id' => 'int',
		'is_read' => 'bool'
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
		return $this->belongsTo(Employee::class);
	}
}
