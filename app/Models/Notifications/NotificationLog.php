<?php


namespace App\Models\Notifications;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\HR\Employee;
use App\Models\Lead\Lead;
use App\Models\Academic\Enrollment;


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
	public $timestamps = true;

	protected $casts = [
		'entity_id' => 'integer',
		'sent_at' => 'datetime',
		'retry_count' => 'integer',
		'created_at' => 'datetime',
		'payload' => 'array',
		'response_payload' => 'array',
        'updated_at' => 'datetime',
        'created_at' => 'datetime'
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

    public function isPending()
    {
        return $this->status === 'Pending';
    }

    public function isSent()
    {
        return $this->status === 'Sent';
    }

    public function isFailed()
    {
        return $this->status === 'Failed';
    }

    public function markSent($response = null)
    {
        $this->update([
            'status' => 'Sent',
            'sent_at' => now(),
            'response_payload' => $response
        ]);
    }

    public function markFailed($response = null)
    {
        $this->update([
            'status' => 'Failed',
            'response_payload' => $response,
            'retry_count' => $this->retry_count + 1
        ]);
    }

    public function scopePending(Builder $query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeSent(Builder $query)
    {
        return $query->where('status', 'Sent');
    }

    public function scopeFailed(Builder $query)
    {
        return $query->where('status', 'Failed');
    }

    public function scopeForEntity(Builder $query, $type, $id)
    {
        return $query->where('entity_type', $type)
                     ->where('entity_id', $id);
    }

    public function canRetry()
    {
        return $this->status === 'Failed'
            && $this->retry_count < 3;
    }

    public function phoneFormatted()
    {
        return preg_replace('/[^0-9]/', '', $this->phone_number);
    }
}
