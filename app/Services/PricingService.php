<?php

namespace App\Services;

use App\Models\Academic\Level;
use App\Models\Academic\Sublevel;
use App\Models\Academic\CourseTemplate;
use App\Models\Finance\PrivateBundle;
use Illuminate\Support\Facades\DB;

class PricingService
{
    /**
     * Calculate course price only.
     * Material and test fee are handled separately — NOT included here.
     * Payment plan applies to the returned final_price only.
     */
    public function calculate($data): array
    {
        // ── PRIVATE: price comes from bundle ──
        if (($data['type'] ?? null) === 'private') {

            if (empty($data['bundle_id'])) {
                return ['base_price' => 0, 'discount' => 0, 'final_price' => 0];
            }

            $bundle = PrivateBundle::find($data['bundle_id']);

            if (!$bundle) {
                throw new \Exception('Invalid bundle selected.');
            }

            return [
                'base_price'  => (float) $bundle->price,
                'discount'    => 0,
                'final_price' => (float) $bundle->price,
            ];
        }

        // ── GROUP: price from sublevel → level → course ──
        $basePrice = $this->getBasePrice($data);

        // Apply offer discount (on course price only)
        $discount = $this->getDiscount($data['course_template_id'] ?? null, $basePrice);

        $finalPrice = max(0, $basePrice - $discount);

        return [
            'base_price'  => round($basePrice, 2),
            'discount'    => round($discount, 2),
            'final_price' => round($finalPrice, 2),
        ];
    }

    // ─────────────────────────────────────────
    // Base price: sublevel > level > course
    // ─────────────────────────────────────────
    private function getBasePrice(array $data): float
    {
        if (!empty($data['sublevel_id'])) {
            $sub = Sublevel::find($data['sublevel_id']);
            if ($sub && $sub->price !== null) return (float) $sub->price;
        }

        if (!empty($data['level_id'])) {
            $level = Level::find($data['level_id']);
            if ($level && $level->price !== null) return (float) $level->price;
        }

        if (!empty($data['course_template_id'])) {
            $course = CourseTemplate::find($data['course_template_id']);
            if ($course && $course->price !== null) return (float) $course->price;
        }

        return 0.0;
    }

    // ─────────────────────────────────────────
    // Active offer discount for this course
    // ─────────────────────────────────────────
    private function getDiscount(?int $courseId, float $price): float
    {
        if (!$courseId || $price <= 0) return 0.0;

        $offer = DB::table('offer')
            ->join('offer_course_template', 'offer.offer_id', '=', 'offer_course_template.offer_id')
            ->where('offer_course_template.course_template_id', $courseId)
            ->where('offer.is_active', 1)
            ->where(function ($q) {
                $q->whereNull('offer.start_date')
                  ->orWhereDate('offer.start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('offer.end_date')
                  ->orWhereDate('offer.end_date', '>=', now());
            })
            ->select('offer.discount_type', 'offer.discount_value')
            ->first();

        if (!$offer) return 0.0;

        if ($offer->discount_type === 'Percentage') {
            return round($price * ($offer->discount_value / 100), 2);
        }

        return min((float) $offer->discount_value, $price);
    }
}