<?php
namespace DancePressTRWA\Models;

final class Option extends Model
{
    public function __construct($sessionCondition = '')
    {
        parent::__construct($sessionCondition);
    }


    /**
     *
     * @return array
     */
    public function getMailingOptions()
    {
        $mailingoptions = array(
                'ds_mailing_signature_image' => $this->getMailingSignatureImage(),
                'ds_mailing_description' => get_option('ds_mailing_description'),
        );

        return $mailingoptions;
    }

    /**
     *
     * @return string|null
     */
    public function getMailingSignatureImage()
    {
        return get_option('ds_mailing_signature_image');
    }

    /**
     *
     * @return array
     */
    public function getContactOptions()
    {
        $contactoptions = array(
                'ds_contact_address' => $this->getContactAddress(),
                'ds_contact_address_province' => $this->getContactAddressProvince(),
                'ds_contact_address_country' => $this->getContactAddressCountry(),
                'ds_contact_address_postal_code' => $this->getContactAddressPostalCode(),
                'ds_contact_telephone' => $this->getContactTelephone(),
                'ds_contact_email' => $this->getContactEmail(),

        );

        return $contactoptions;
    }

    public function getContactAddress()
    {
        return get_option('ds_contact_address');
    }

    public function getContactAddressProvince()
    {
        return get_option('ds_contact_address_province');
    }

    public function getContactAddressCountry()
    {
        return get_option('ds_contact_address_country', 38);
    }

    public function getContactAddressPostalCode()
    {
        return get_option('ds_contact_address_postal_code');
    }

    /**
     *
     * @return string|null
     */
    public function getContactEmail()
    {
        return get_option('ds_contact_email');
    }

    /**
     *
     * @return string|null
     */
    public function getContactTelephone()
    {
        return get_option('ds_contact_telephone');
    }

    /**
     * @return array
     */
    public function getStripeOptions()
    {
        $stripeoptions = array(
                'ds_stripe_percent' => $this->getStripePercent(),
                'ds_stripe_decimal' => get_option('ds_stripe_decimal'),
                'ds_stripe_secret_key' => $this->getStripeSecretKey(),
                'ds_stripe_publishable_key' => $this->getStripePublishableKey(),
                'ds_stripe_currency' => $this->getStripeCurrency()
        );

        return $stripeoptions;
    }

    /**
     *
     * @return double|null
     */
    public function getStripePercent()
    {
        return get_option('ds_stripe_percent');
    }

    /**
     *
     * @return string|null
     */
    public function getStripeSecretKey()
    {
        return get_option('ds_stripe_secret_key');
    }

    /**
     *
     * @return string|null
     */
    public function getStripePublishableKey()
    {
        return get_option('ds_stripe_publishable_key');
    }

    public function getStripeCurrency()
    {
        return get_option('ds_stripe_currency');
    }

    /**
     *
     * @return array
     */
    public function getFeeOptions()
    {
        $feeoptions = array(
                'ds_registration_fee' => $this->getRegistrationFee(),
                'ds_classes_one_time_flat_fee' => $this->getClassesOneTimeFlatFee(),
                'ds_costume_deposit' => $this->getCostumeDepositFee(),
                'ds_tax_percent' => $this->getTaxPercent(),
                'ds_installment_fees' => $this->getInstallmentFees()
        );

        return $feeoptions;
    }

    /**
     *
     * @return double|null
     */
    public function getRegistrationFee()
    {
        return get_option('ds_registration_fee');
    }

    /**
     *
     * @return double|null
     */
    public function getClassesOneTimeFlatFee()
    {
        return get_option('ds_classes_one_time_flat_fee');
    }

    /**
     *
     * @return double|null
     */
    public function getCostumeDepositFee()
    {
        return get_option('ds_costume_deposit');
    }

    /**
     *
     * @return double|null
     */
    public function getTaxPercent()
    {
        return get_option('ds_tax_percent');
    }

    /**
     *
     * @return array
     */
    public function getInstallmentFees($formatted = false)
    {
        $installment_fees =  get_option('ds_installment_fees');

        if ($formatted && !empty($installment_fees)) {
            $installment_fees = self::formatSubmittedInstallmentFees($installment_fees);
        }

        return is_array($installment_fees) ? $installment_fees : [];
    }

    /**
     * @throws \Exception
     * @param array $ds_installment_fees
     * @return array
     */
    public static function formatSubmittedInstallmentFees($ds_installment_fees)
    {
        if (empty($ds_installment_fees)) {
            return $ds_installment_fees;
        }

        $installments = [];
        $installment_names = [];
        $installment_dates = [];

        foreach (['name', 'date', 'amount'] as  $key) {
            if (count($ds_installment_fees[$key]) > 12) {
                throw new \Exception(" The maximum number of installments allowed is 12");
                return false;
            }

            foreach ($ds_installment_fees[$key] as $position => $installment_key_value) {
                if (!$installment_key_value) {
                    throw new \Exception("Property '{$key}' was not set for the installment at position #" . ($position + 1));
                    return false;
                }

                if ($key == 'name') {
                    if (in_array($installment_key_value, $installment_names)) {
                        throw new \Exception("Installment names must be unique");
                        return false;
                    }

                    $installment_names[] = $installment_key_value;
                }

                if ($key == 'date') {
                    if (in_array($installment_key_value, $installment_dates)) {
                        throw new \Exception("Installment dates must be unique");
                        return false;
                    }

                    $installment_dates[] = $installment_key_value;
                }

                $installments[$position][$key] = $installment_key_value;
            }
        }

        return $installments;
    }
}
