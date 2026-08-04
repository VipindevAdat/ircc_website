<?php

namespace App\Support;

use App\Traits\GivebutterHelper;

/**
 * Server-side coupon rules (customer checkout / PaymentController, admin add payment).
 * All discounts apply only to unpaid student-line totals; adult attendee amounts stay full price.
 * Exception: IRCC1 charges a flat $1.00 total (entire checkout), not student-only.
 * RISE150: unpaid regular-rate student slots repriced toward early-bird (computed in GivebutterHelper::user_payments).
 */
class PaymentCoupon
{
    public const VALID_CODES = ['Save10', 'Save5', 'Save25', 'RISE150', 'LIFO10', 'IRCC1'];

    public const RISE150_LABEL = 'RISE early bird benefit activated';

    /**
     * Normalize user input (e.g. rise150 → RISE150) before validation / storage.
     */
    public static function canonicalize(string $code): string
    {
        $code = trim($code);
        if (strcasecmp($code, 'RISE150') === 0) {
            return 'RISE150';
        }

        return $code;
    }

    public static function isValid(?string $code): bool
    {
        $code = trim((string) $code);

        if ($code === '') {
            return true;
        }

        $code = self::canonicalize($code);

        return in_array($code, self::VALID_CODES, true);
    }

    /**
     * RISE150 dollar discount for unpaid regular-rate students (must match dashboard amount payable math).
     */
    public static function rise150UnpaidStudentDiscountAmount(int $registrationId): float
    {
        $calculator = new class
        {
            use GivebutterHelper;
        };

        $summary = $calculator->user_payments($registrationId);

        return (float) str_replace(',', '', $summary['rise150_student_discount'] ?? '0');
    }

    /**
     * @param  float|null  $explicitTotalWhenNoCoupon  When no coupon: charge this total (e.g. admin override) instead of student+adult from summary
     * @return array{amount: float, discount_amount: float, original_amount: float, coupon_code: ?string}
     */
    public static function apply(
        ?string $couponCode,
        float $studentPayable,
        float $adultPayable,
        ?int $registrationId = null,
        ?float $explicitTotalWhenNoCoupon = null
    ): array {
        $studentPayable = round(max(0, $studentPayable), 2);
        $adultPayable = round(max(0, $adultPayable), 2);
        $defaultTotal = round($studentPayable + $adultPayable, 2);

        $couponCode = self::canonicalize(trim((string) $couponCode));

        if ($couponCode === '') {
            $total = ($explicitTotalWhenNoCoupon !== null && is_finite($explicitTotalWhenNoCoupon) && $explicitTotalWhenNoCoupon >= 0)
                ? round($explicitTotalWhenNoCoupon, 2)
                : $defaultTotal;

            return [
                'amount' => $total,
                'discount_amount' => 0.0,
                'original_amount' => $total,
                'coupon_code' => null,
            ];
        }

        if (! in_array($couponCode, self::VALID_CODES, true)) {
            throw new \InvalidArgumentException('Invalid coupon code.');
        }

        $originalAmount = $defaultTotal;

        if ($couponCode === 'RISE150') {
            if ($registrationId === null) {
                throw new \InvalidArgumentException('Invalid coupon code.');
            }
            $studentDiscount = self::rise150UnpaidStudentDiscountAmount($registrationId);
            $discountAmount = round(min($studentDiscount, $studentPayable), 2);
            $amount = round(max($adultPayable + $studentPayable - $discountAmount, 0), 2);

            return [
                'amount' => (float) $amount,
                'discount_amount' => (float) $discountAmount,
                'original_amount' => $originalAmount,
                'coupon_code' => 'RISE150',
            ];
        }

        if ($couponCode === 'IRCC1') {
            $amount = 1.00;
            $discountAmount = round(max(0, $originalAmount - 1.00), 2);
        } else {
            $percentMap = [
                'Save5' => 5,
                'Save10' => 10,
                'Save25' => 25,
                'LIFO10' => 10,
            ];
            $percent = $percentMap[$couponCode] ?? 0;
            $discountAmount = $percent > 0 ? round(($studentPayable * $percent) / 100, 2) : 0.0;
            $amount = round(max($adultPayable + $studentPayable - $discountAmount, 0), 2);
        }

        return [
            'amount' => round((float) $amount, 2),
            'discount_amount' => round((float) $discountAmount, 2),
            'original_amount' => $originalAmount,
            'coupon_code' => $couponCode,
        ];
    }
}
