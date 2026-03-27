<?php

namespace App\Services;

use App\Models\Academic\Level;
use App\Models\Academic\Sublevel;
use App\Models\Finance\Offer;
use App\Models\Finance\OfferCourseTemplate;
use Carbon\Carbon;

class PricingService
{
    public function calculatePrice($data)
    {
        // 1. Base Price
        $price = $this->getBasePrice($data);

        // 2. Offer
        $price = $this->applyOffer($data['course_template_id'], $price);

        // 3. Manual Discount
        if (!empty($data['discount_value'])) {
            $price -= $data['discount_value'];
        }

        return max($price, 0);
    }

    private function getBasePrice($data)
    {
        if (!empty($data['sublevel_id'])) {
            $sub = Sublevel::find($data['sublevel_id']);

            if ($sub && $sub->price_override) {
                return $sub->price_override;
            }
        }

        $level = Level::findOrFail($data['level_id']);

        return $level->price;
    }

    private function applyOffer($courseTemplateId, $price)
    {
        $offer = Offer::where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->whereHas('courseTemplates', function ($q) use ($courseTemplateId) {
                $q->where('course_template_id', $courseTemplateId);
            })
            ->first();

        if (!$offer) return $price;

        if ($offer->discount_type === 'Percentage') {
            return $price - ($price * ($offer->discount_value / 100));
        }

        return $price - $offer->discount_value;
    }
}

