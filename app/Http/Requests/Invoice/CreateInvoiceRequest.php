<?php

namespace App\Http\Requests\Invoice;

use App\Http\Requests\FormRequest;

class CreateInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'total_voucher' => 'nullable|integer',
            'total_amount' => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'merchant_id' => 'required|integer|exists:merchants,id',
            'note' => 'nullable|string',
            'tax' => 'nullable|boolean',
            'temp_journals' => 'nullable|array',
            'temp_journals.*id' => 'nullable|integer|exists:temp_journals,id',
            // 'temp_journals.*.debit_account_id' => 'nullable',
            // 'temp_journals.*.credit_account_id' => 'nullable',
            // 'temp_journals.*.amount' => 'nullable',
            // 'temp_journals.*.resourceable_id' => 'nullable',
            // 'temp_journals.*.resourceable_type' => 'nullable',
            // 'temp_journals.*.status' => 'nullable',
            // 'temp_journals.*.thirdparty_invoice' => 'nullable|string',
            // 'temp_journals.*.voucher_no' => 'nullable|string',
            // 'temp_journals.*.pickup_date' => 'nullable',
            // 'temp_journals.*.delivered_date' => 'nullable',
            // 'temp_journals.*.receiver_name' => 'nullable|string',
            // 'temp_journals.*.receiver_address' => 'nullable|string',
            // 'temp_journals.*.receiver_phone' => 'nullable|string',
            // 'temp_journals.*.receiver_city' => 'nullable|string',
            // 'temp_journals.*.receiver_zone' => 'nullable|string',
            // 'temp_journals.*.total_amount_to_collect' => 'nullable',
            // 'temp_journals.*.voucher_remark' => 'nullable|string',
        ];
    }
}