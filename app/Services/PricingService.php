<?php

namespace App\Services;

use App\Models\Academic\Level;
use App\Models\Academic\Sublevel;
use App\Models\Academic\CourseTemplate;
use App\Models\Finance\PrivateBundle;

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

    
}