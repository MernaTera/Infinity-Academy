<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Academic\CourseTemplate;
use App\Models\Finance\Offer;

/**
 * Class OfferCourseTemplate
 * 
 * @property int $offer_id
 * @property int $course_template_id
 * 
 * @property CourseTemplate $course_template
 * @property Offer $offer
 *
 * @package App\Models
 */
class OfferCourseTemplate extends Model
{
	protected $table = 'offer_course_template';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'offer_id' => 'integer',
		'course_template_id' => 'integer'
	];

	public function courseTemplate()
	{
		return $this->belongsTo(CourseTemplate::class, 'course_template_id');
	}

	public function offer()
	{
		return $this->belongsTo(Offer::class, 'offer_id');
	}

	public function isActiveOffer()
	{
		if (!$this->offer) {
			return false;
		}
		return $this->offer->isActive();
	}
}