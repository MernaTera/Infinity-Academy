<?php

namespace App\Services;

use App\Models\Academic\Level;
use App\Models\Academic\Sublevel;
use App\Models\Academic\CourseTemplate;

class PricingService
{
    public function calculate($data)
    {
        $price = $this->getBasePrice($data);

        $discount = $data['discount_value'] ?? 0;

        $final = $price - $discount;

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

            if ($sub && $sub->price_override) {
                return $sub->price_override;
            }
        }

        if (!empty($data['level_id'])) {
            $level = Level::find($data['level_id']);

            if ($level) {
                return $level->price;
            }
        }

        if (!empty($data['course_template_id'])) {
            $course = CourseTemplate::findOrFail($data['course_template_id']);

            return $course->price ?? 0;
        }

        throw new \Exception('Cannot determine price');
    }
}