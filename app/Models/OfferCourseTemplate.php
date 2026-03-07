<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
		'offer_id' => 'int',
		'course_template_id' => 'int'
	];

	public function course_template()
	{
		return $this->belongsTo(CourseTemplate::class);
	}

	public function offer()
	{
		return $this->belongsTo(Offer::class);
	}
}
