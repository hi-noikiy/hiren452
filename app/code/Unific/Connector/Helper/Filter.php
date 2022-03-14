<?php

namespace Unific\Connector\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Filter extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $orderWhiteList = [
        'customer_email',
        'customer_firstname',
        'customer_middlename',
        'customer_lastname',
        'customer_group_id',
        'customer_is_guest',
        'discount_amount',
        'discount_description',
        'grand_total',
        'discount_tax_compensation_amount',
        'increment_id',
        'is_virtual',
        'order_currency_code',
        'quote_id',
        'remote_ip',
        'shipping_amount',
        'shipping_description',
        'shipping_discount_amount',
        'shipping_discount_tax_compensation_amount',
        'shipping_incl_tax',
        'store_id',
        'subtotal',
        'subtotal_incl_tax',
        'tax_amount',
        'total_due',
        'total_qty_ordered',
        'weight',
        'gift_message_id',
        'state',
        'status',
        'store_name',
        'total_item_count',
        'protect_code',
        'entity_id',
        'created_at',
        'updated_at',
        'shipping_method',
        'addresses',
        'order_items',
        'payment',
        'status_histories'
    ];

    protected $paymentWhitelist = [
        'method',
        'additional_data',
        'additional_information',
        'po_number',
        'amount_ordered',
        'shipping_amount'
    ];

    protected $itemsWhitelist = [
        'sku',
        'name',
        'description',
        'weight',
        'qty',
        'qty_ordered',
        'qty_canceled',
        'qty_invoiced',
        'qty_shipped',
        'qty_refunded',
        'is_virtual',
        'original_price',
        'additional_data',
        'price',
        'tax_percent',
        'tax_amount',
        'tax_before_discount',
        'row_weight',
        'row_total',
        'discount_tax_compensation_amount',
        'product_id',
        'product_type',
        'qty_backordered',
        'free_shipping',
        'price_incl_tax',
        'row_total_incl_tax',
        'discount_percent',
        'discount_amount',
        'product_options',
        'item_id',
        'created_at',
        'updated_at'
    ];

    protected $checkoutWhitelist = [
        'entity_id',
        'store_id',
        'created_at',
        'updated_at',
        'converted_at',
        'is_active',
        'is_virtual',
        'is_multi_shipping',
        'items_count',
        'items_qty',
        'orig_order_id',
        'quote_currency_code',
        'grand_total',
        'checkout_method',
        'customer_id',
        'customer_tax_class_id',
        'customer_group_id',
        'customer_email',
        'customer_prefix',
        'customer_firstname',
        'customer_middlename',
        'customer_lastname',
        'customer_suffix',
        'customer_postcode',
        'customer_street',
        'customer_street2',
        'customer_street3',
        'customer_city',
        'customer_telephone',
        'customer_fax',
        'customer_company',
        'customer_region',
        'customer_country',
        'customer_dob',
        'customer_note',
        'customer_is_guest',
        'remote_ip',
        'reserved_order_id',
        'coupon_code',
        'customer_taxvat',
        'customer_gender',
        'subtotal',
        'subtotal_with_discount',
        'is_changed',
        'ext_shipping_info',
        'items',
        'addresses',
        'masked_id',
        'abandoned_checkout_url',
        'customer'
    ];

    protected $addressWhitelist = [
        'street',
        'street1',
        'street2',
        'street3',
        'street4',
        'street5',
        'city',
        'region',
        'country_id',
        'postcode',
        'email',
        'firstname',
        'lastname',
        'company',
        'telephone',
        'region_code',
        'region_id',
        'fax'
    ];

    /**
     * @return array
     */
    public function getOrderWhiteList()
    {
        return $this->orderWhiteList;
    }

    /**
     * @return array
     */
    public function getPaymentWhitelist()
    {
        return $this->paymentWhitelist;
    }

    /**
     * @return array
     */
    public function getItemsWhitelist()
    {
        return $this->itemsWhitelist;
    }

    /**
     * @return array
     */
    public function getCheckoutWhitelist()
    {
        return $this->checkoutWhitelist;
    }

    /**
     * @return array
     */
    public function getAddressWhitelist()
    {
        return $this->addressWhitelist;
    }

    /**
     * Ensure the data is always sanitized
     *
     * @param $returnData
     * @return mixed
     */
    public function sanitizeAddressData($returnData)
    {
        if (isset($returnData['addresses'])) {
            if (isset($returnData['addresses']['shipping'])) {

                if (isset($returnData['addresses']['shipping']['0'])) {
                    $returnData['addresses']['shipping']['street'] = $returnData['addresses']['shipping']['0'];
                    unset($returnData['addresses']['shipping']['0']);
                }

                $returnData['addresses']['shipping'] = array_intersect_key(
                    $returnData['addresses']['shipping'],
                    array_flip($this->addressWhitelist)
                );
            }

            if (isset($returnData['addresses']['billing'])) {
                $returnData['addresses']['billing'] = array_intersect_key(
                    $returnData['addresses']['billing'],
                    array_flip($this->addressWhitelist)
                );
            }
        }

        return $returnData;
    }

    public function fixAddressKey($returnData, $identifier = 'street')
    {
        for ($i=0; $i<10; $i++) {
            if (isset($returnData[$i])) {
                $arrayKey = ($i>0) ? $identifier . $i : $identifier;
                $returnData[$arrayKey] = $returnData[$i];
                unset($returnData[$i]);
            }
        }

        return $returnData;
    }
}
