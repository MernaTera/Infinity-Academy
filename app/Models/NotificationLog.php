<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NotificationLog
 * 
 * @property int $notification_id
 * @property string $entity_type
 * @property int $entity_id
 * @property string $notification_type
 * @property string $phone_number
 * @property string|null $message_template
 * @property string|null $payload
 * @property Carbon|null $sent_at
 * @property string $status
 * @property int|null $retry_count
 * @property string|null $response_payload
 * @property Carbon|null $created_at
 *
 * @package App\Models
 */
class NotificationLog extends Model
{
	protected $table = 'notification_log';
	protected $primaryKey = 'notification_id';
	public $timestamps = false;

	protected $casts = [
		'entity_id' => 'int',
		'sent_at' => 'datetime',
		'retry_count' => 'int'
	];

	protected $fillable = [
		'entity_type',
		'entity_id',
		'notification_type',
		'phone_number',
		'message_template',
		'payload',
		'sent_at',
		'status',
		'retry_count',
		'response_payload'
	];
}
