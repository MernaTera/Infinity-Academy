<?php

namespace App\Services;

use App\Models\Academic\Level;
use App\Models\Academic\Sublevel;
use App\Models\Academic\CourseTemplate;
use App\Models\Finance\PrivateBundle;
use App\Models\Finance\Offer;
use App\Models\Finance\OfferCourseTemplate;

class PricingService
{
    public function calculate($data)
    {
        // 🔵 PRIVATE
        if (($data['type'] ?? null) === 'private') {

            if (empty($data['bundle_id'])) {
                return [
                    'base_price' => 0,
                    'discount' => 0,
                    'final_price' => 0
                ];
            }

            $bundle = \App\Models\Finance\PrivateBundle::find($data['bundle_id']);

            if (!$bundle) {
                throw new \Exception('Invalid bundle');
            }

            $price = $bundle->price;
        }

        // 🟢 GROUP
        else {
            $price = $this->getBasePrice($data);
        }

        // discount
        $discount = $this->getDiscount($data['course_template_id'] ?? null, $price);

        $final = $price - $discount;

        if (!empty($data['material_price'])) {
            $final += $data['material_price'];
        }

        return [
            'base_price' => $price,
            'discount' => $discount,
            'final_price' => max($final, 0)
        ];
    }

    private function getBasePrice($data)
    {
        if (!empty($data['sublevel_id'])) {
            $sub = Sublevel::find($data['sublevel_id']);

            if ($sub && $sub->price !== null) {
                return $sub->price;
            }
        }

        if (!empty($data['level_id'])) {
            $level = Level::find($data['level_id']);

            if ($level && $level->price !== null) {
                return $level->price;
            }
        }

        if (!empty($data['course_template_id'])) {
            $course = CourseTemplate::find($data['course_template_id']);

            if ($course && $course->price !== null) {
                return $course->price;
            }
        }

        return 0;
    }

    private function getDiscount($courseId, $price)
    {
        if (!$courseId) return 0;

        $offer = \DB::table('offer')
            ->join('offer_course_template', 'offer.offer_id', '=', 'offer_course_template.offer_id')
            ->where('offer_course_template.course_template_id', $courseId)
            ->where('offer.is_active', 1)
            ->whereDate('offer.start_date', '<=', now())
            ->whereDate('offer.end_date', '>=', now())
            ->select('offer.*')
            ->first();

        if (!$offer) return 0;

        if ($offer->discount_type === 'Percentage') {
            return $price * ($offer->discount_value / 100);
        }

        return $offer->discount_value;
    }

    
}