<?php
namespace DancePressTRWA\Util;

//DancePressTRWA
use DancePressTRWA\Models\Option as Option;

final class FinancialCalculations
{
    /**
     * calculateTotalFees
     * Does financial calculations
     * @param array $pricing
     * @throws \Exception
     * @return \stdClass object containing fee calculations
     */
    public static function calculateTotalFees(array $pricing)
    {
        $totalFees = new \stdClass();
        $option = new Option();

        //array("classes" => $classes, "scheduled_payments" => $scheduledPayments, "total_classes_one_time_amount" => $totalOneTimeFlatFee);

        $registrationFee = $option->getRegistrationFee();
        $stripePercent = $option->getStripePercent();

        if (!$registrationFee) {
            $registrationFee = 0;
        }

        $costumeDeposits = 0;

        $classDetails = $pricing["classes"]; //$this->getClassDetails();

        foreach ($classDetails as $class) {
            $costumeDeposits += ($class->costume_fee * $class->student_count);
        }

        $totalFees->totalClassesOneTimeAmount = $pricing['total_classes_one_time_amount'];
        $totalFees->registrationFee = (float) $registrationFee;
        $totalFees->costumeDeposit = $costumeDeposits;
        $totalFees->subTotal = $totalFees->registrationFee + $totalFees->costumeDeposit + $totalFees->totalClassesOneTimeAmount;
        $totalFees->grandTotal = $totalFees->subTotal;
        $totalFees->classesOneTimeAmountPlusRegistration = $totalFees->registrationFee + $totalFees->totalClassesOneTimeAmount;

        //Undo crazy reverse calcs of tax requested by orig client.

        $totalFees->payableNowBaseTax = DANCEPRESSTRWA_TAX_MULTIPLIER ? $totalFees->classesOneTimeAmountPlusRegistration *  DANCEPRESSTRWA_TAX_MULTIPLIER : 0;
        if ($stripePercent) {
            $totalFees->payableNowTransFee = ($totalFees->classesOneTimeAmountPlusRegistration * $stripePercent) - $totalFees->classesOneTimeAmountPlusRegistration;
        } else {
            $totalFees->payableNowTransFee = $totalFees->classesOneTimeAmountPlusRegistration/100;
        }

        $totalFees->payableNowFeeTax = DANCEPRESSTRWA_TAX_DIVIDER ? $totalFees->payableNowTransFee * DANCEPRESSTRWA_TAX_DIVIDER : 0;
        $totalFees->payableNowFeeIncTax = $totalFees->payableNowFeeTax + $totalFees->payableNowTransFee;
        $totalFees->payableNowIncTax = $totalFees->classesOneTimeAmountPlusRegistration + $totalFees->payableNowBaseTax + $totalFees->payableNowTransFee + $totalFees->payableNowFeeTax;

        $totalFees->payableNowIncTaxRegFeeOnly = $totalFees->payableNowIncTax;

        //This number not needed now tax is not back-calculated
        $totalFees->payableNowExclTax = $totalFees->subTotal;
        $totalFees->taxNow = $totalFees->payableNowIncTax - $totalFees->payableNowExclTax;

        //NOTE TONIGHT!!!
        //Here - remains to be done:
        //adjust class fee based on total number of classes registered.
        if (!empty($pricing['scheduled_payments'])) {
            $totalFees->periods = [];

            foreach ($pricing['scheduled_payments'] as $scheduled_payment_date => $scheduled_payment) {
                $scheduled_payment_name = $scheduled_payment['payment_name'];
                $scheduled_payment_amount = $scheduled_payment['amount'];

                $base = $scheduled_payment_amount;
                $baseTax = DANCEPRESSTRWA_TAX_MULTIPLIER ? $base * DANCEPRESSTRWA_TAX_MULTIPLIER : 0;
                if ($stripePercent) {
                    $transFee = $scheduled_payment_amount > 0 ? ($scheduled_payment_amount /100) * $stripePercent : 0;
                } else {
                    $transFee = 0;
                }

                $feeTax = DANCEPRESSTRWA_TAX_MULTIPLIER ? $transFee * DANCEPRESSTRWA_TAX_MULTIPLIER : 0;
                $feeIncTax = $transFee + $feeTax;
                $incTax = $base + $baseTax + $feeIncTax;
                $exclTax = $base + $transFee;
                $tax = $incTax - $exclTax;
                $totalFees->grandTotal += $incTax;

                $totalFees->periods[]= [
                    'base' => $base,
                    'baseTax' => $baseTax,
                    'transFee' => $transFee,
                    'feeTax' => $feeTax,
                    'feeIncTax' => $feeIncTax,
                    'incTax' => $incTax,
                    'exclTax' => $exclTax,
                    'tax' => $tax,
                    'payment_name' => $scheduled_payment_name,
                    'payment_date' => $scheduled_payment_date
                ];

                if (time() > strtotime($scheduled_payment_date)) {
                    $totalFees->payableNowIncTax += $incTax;
                }
            }
        }
        $_SESSION['totalFees'] = $totalFees;
        return $totalFees;
    }
}
