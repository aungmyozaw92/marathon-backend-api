<?php

namespace App\Repositories\Web\Api\v1;

use Event;
use App\Models\Staff;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\Voucher;
use App\Models\Attachment;
use App\Models\TempJournal;
use App\Services\SmsService;
use App\Models\InvoiceHistory;
use App\Models\InvoiceJournal;
use App\Models\InvoiceVoucher;
use App\Repositories\BaseRepository;
use App\Contracts\MembershipContract;
use App\Observers\TempJournalObserver;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Web\Api\v1\AccountRepository;
use App\Repositories\Web\Api\v1\JournalRepository;
use App\Repositories\Web\Api\v1\VoucherRepository;
use App\Repositories\Web\Api\v1\CustomerRepository;
use App\Repositories\Web\Api\v1\MerchantRepository;

class InvoiceRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Invoice::class;
    }

    /**
     * @param array $data
     *
     * @return Invoice
     */

    public function create(array $data): Invoice
    {
        if (isset($data['select_all']) && $data['select_all']) {
            $temp_journals = TempJournal::where('merchant_id', $data['merchant_id'])
                                        ->where('status', 0)
                                        ->filter(request()->only(['start_date', 'end_date']))
                                        ->get();
            $data['total_voucher'] = $temp_journals->count();
            $invoice = $this->create_with_seleted_all($data, $temp_journals);                         
        }else{
            $invoice = $this->create_with_seleted($data);
        }
        return $invoice->refresh();
    }

    public function create_with_seleted($data)
    {
        if (isset($data['tax']) && $data['tax']) {
            $tax_amount = $data['total_amount'] *  0.05;
        }else{
            $tax_amount = 0;
        }
        $invoice =  Invoice::create([
            'total_voucher'         => $data['total_voucher'],
            'total_amount'     => $data['total_amount'] - $tax_amount,
            'merchant_id' => isset($data['merchant_id']) ? $data['merchant_id']: null,
            'city_id' => auth()->user()->city_id,
            'note'        => isset($data['note']) ? getConvertedString($data['note']) : null,
            'payment_status'  => isset($data['payment_status']) ? $data['payment_status'] : 0,
            'is_pointable'  => isset($data['is_pointable']) ? $data['is_pointable'] : 0,
            'tax'  => isset($data['tax']) ? $data['tax'] : 0,
            'created_by' => auth()->user()->id,
        ]);
        // $invoice->vouchers()->syncWithoutDetaching($data['vouchers']);
        $tempIds = array();

        if (isset($data['temp_journals'])) {
            foreach ($data['temp_journals'] as $temp) {
                $temp_journal = TempJournal::findOrFail($temp['id']);
                if (!$temp_journal->status) {
                    $invoice_journal =  InvoiceJournal::create([
                        'note' => $temp_journal->voucher_remark,
                        'invoice_id' => $invoice->id,
                        'merchant_id' => $invoice->merchant_id,
                        'invoice_no' => $invoice->invoice_no,
                        'temp_journal_id' => $temp_journal->id,
                        'debit_account_id' => $temp_journal->debit_account_id,
                        'credit_account_id' => $temp_journal->credit_account_id,
                        'amount' => $temp_journal->amount,
                        'resourceable_id' => $temp_journal->resourceable_id,
                        'resourceable_type' => $temp_journal->resourceable_type,
                        'status' => $temp_journal->status,
                        'thirdparty_invoice' => $temp_journal->thirdparty_invoice,
                        'voucher_no' => $temp_journal->voucher_no,
                        'pickup_date' => $temp_journal->pickup_date,
                        'delivered_date' => $temp_journal->delivered_date,
                        'receiver_name' => $temp_journal->receiver_name,
                        'receiver_address' => $temp_journal->receiver_address,
                        'receiver_phone' => $temp_journal->receiver_phone,
                        'receiver_city' => $temp_journal->receiver_city,
                        'receiver_zone' => $temp_journal->receiver_zone,
                        'total_amount_to_collect' => $temp_journal->total_amount_to_collect,
                        'voucher_remark' => $temp_journal->voucher_remark,
                        'delivery_status_id' => $temp_journal->delivery_status_id,
                        'delivery_status' => $temp_journal->delivery_status,
                        'weight' => $temp_journal->weight,
                        'created_by' => auth()->user()->id,
                    ]);
                    if ($invoice_journal) {
                        array_push($tempIds, $temp_journal->id);
                    }
                }
            }
            TempJournal::whereIn('id', $tempIds)->update(array('status' => 1));
        }

        return $invoice->refresh();
    }

    public function create_with_seleted_all($data, $temp_journals)
    {
        $invoice =  Invoice::create([
            'total_voucher'         => $data['total_voucher'],
            'total_amount'     => isset($data['total_amount']) ? $data['total_amount']: 0,
            'merchant_id' => isset($data['merchant_id']) ? $data['merchant_id']: null,
            'city_id' => auth()->user()->city_id,
            'note'        => isset($data['note']) ? getConvertedString($data['note']) : null,
            'payment_status'  => isset($data['payment_status']) ? $data['payment_status'] : 0,
            'is_pointable'  => isset($data['is_pointable']) ? $data['is_pointable'] : 0,
            'tax'  => isset($data['tax']) ? $data['tax'] : 0,
            'created_by' => auth()->user()->id,
        ]);

        $total_amount = 0;
        $tempIds = array();

        foreach ($temp_journals->chunk(500) as $temps) {
            foreach ($temps as $temp_journal) {

                $invoice_journal =  InvoiceJournal::create([
                    'note' => $temp_journal->voucher_remark,
                    'invoice_id' => $invoice->id,
                    'merchant_id' => $invoice->merchant_id,
                    'invoice_no' => $invoice->invoice_no,
                    'temp_journal_id' => $temp_journal->id,
                    'debit_account_id' => $temp_journal->debit_account_id,
                    'credit_account_id' => $temp_journal->credit_account_id,
                    'amount' => $temp_journal->amount,
                    'resourceable_id' => $temp_journal->resourceable_id,
                    'resourceable_type' => $temp_journal->resourceable_type,
                    'status' => $temp_journal->status,
                    'thirdparty_invoice' => $temp_journal->thirdparty_invoice,
                    'voucher_no' => $temp_journal->voucher_no,
                    'pickup_date' => $temp_journal->pickup_date,
                    'delivered_date' => $temp_journal->delivered_date,
                    'receiver_name' => $temp_journal->receiver_name,
                    'receiver_address' => $temp_journal->receiver_address,
                    'receiver_phone' => $temp_journal->receiver_phone,
                    'receiver_city' => $temp_journal->receiver_city,
                    'receiver_zone' => $temp_journal->receiver_zone,
                    'total_amount_to_collect' => $temp_journal->total_amount_to_collect,
                    'voucher_remark' => $temp_journal->voucher_remark,
                    'delivery_status_id' => $temp_journal->delivery_status_id,
                    'delivery_status' => $temp_journal->delivery_status,
                    'weight' => $temp_journal->weight,
                    'created_by' => auth()->user()->id,
                ]);
            
                if ($invoice_journal) {
                    array_push($tempIds, $temp_journal->id);
                    $total_amount += $temp_journal->amount;
                }
                
            }
        }
         
        TempJournal::whereIn('id', $tempIds)->update(array('status' => 1));

        if (isset($data['tax']) && $data['tax']) {
            $tax_amount = $total_amount *  0.05;
        }else{
            $tax_amount = 0;
        }

        $invoice->total_amount = $total_amount - $tax_amount;
        $invoice->save();

        return $invoice->refresh();
    }

    public function update(Invoice $invoice, array $data) : Invoice
    {
        if (isset($data['tax']) && $data['tax']) {
            $tax_amount = $invoice->total_amount *  0.05;
        }else{
            $tax_amount = 0;
        }
        $invoice->note = isset($data['note'])?$data['note']:$invoice->note;
        $invoice->total_amount = $invoice->total_amount - $tax_amount;
        if ($invoice->isDirty()) {
            $invoice->updated_by = auth()->user()->id;
            $invoice->save();
        }

        return $invoice->refresh();
    }

    public function confirm(Invoice $invoice) : Invoice
    {
        $invoice->payment_status = 1;
        if ($invoice->isDirty()) {
            $invoice->updated_by = auth()->user()->id;
            $invoice->save();
        }

        $invoice->invoice_journals()->update(['status' => 1]);
       
        return $invoice->refresh();
    }

    public function update_adjustment_amount(InvoiceJournal $invoice_journal, array $data)
    {
        $invoice = Invoice::find($invoice_journal->invoice_id);

        if($data['amount'] >= $invoice_journal->amount){
            $logStatusId =  getStatusId('add_delivery_invoice');
        }else{
            $logStatusId =  getStatusId('deduce_delivery_invoice');
        }
        $previous = $invoice_journal->amount;

        $invoice_journal->amount = $data['amount'];
        $invoice_journal->adjustment_amount = $data['amount'];
        $invoice_journal->diff_adjustment_amount = $data['amount'] - $invoice_journal->amount ;
        $invoice_journal->is_dirty = 1;
        $invoice_journal->adjustment_by = auth()->user()->id;
        $invoice_journal->adjustment_by_name = auth()->user()->name;
        $invoice_journal->adjustment_date = now();
        $invoice_journal->adjustment_note = isset($data['note']) ? $data['note'] : null;

        if ($invoice_journal->isDirty()) {	
            $invoice_journal->updated_by = auth()->user()->id;
            $invoice_journal->save();
        }

        if ($invoice->tax) {
            $tax_amount = $data['amount'] *  0.05;
        }else{
            $tax_amount = 0;
        }
        $total_amount =  ($invoice->total_amount - $previous) + ($data['amount'] - $tax_amount);
        $invoice->total_amount = $total_amount;
        $invoice->save();

        InvoiceHistory::create([
            'invoice_id' => $invoice->id,
            'invoice_journal_id' => $invoice_journal->id,
            'log_status_id' => $logStatusId,
            'previous' => $previous,
            'next' => $data['amount'],
            'remark' => 'Voucher no - ' . $invoice_journal->voucher_no ,
            'created_by' => auth()->user()->id,
        ]);

        return $invoice_journal->refresh();
    }

    public function remove_voucher(Invoice $invoice, array $data)
    {
        $invoice_journal = InvoiceJournal::where('invoice_id',$invoice->id)
                                        ->where('temp_journal_id',$data['temp_journal_id'])
                                        ->first();

        if (!$invoice_journal) {
            $responses = [
                'status' => 1,
                'message' => 'This journal does not include in this invoice'
            ];
            return $responses;
        }
        if ($invoice->tax) {
            $tax_amount = $invoice_journal->amount *  0.05;
        }else{
            $tax_amount = 0;
        }
        $journal_amount = $invoice_journal->amount - $tax_amount;
        $amount = $invoice->total_amount - $journal_amount;

        $invoice_journal->temp_journal()->update(['status' => 0]);

        $invoice->total_amount = $amount;
        $invoice->total_voucher = $invoice->total_voucher - 1;

        if ($invoice->isDirty()) {	
            $invoice->updated_by = auth()->user()->id;
            $invoice->save();
        }

        $invoice_journal->deleted_by = auth()->user()->id;
        $invoice_journal->deleted_at = now();
        $invoice_journal->save();

        $responses = [
            'status' => 1,
            'message' => 'Removed successful'
        ];
        return $responses;
    }

    public function add_voucher(Invoice $invoice, $temp_journal)
    {
        $invoice_journal =  InvoiceJournal::create([
            'note' => null,
            'invoice_id' => $invoice->id,
            'merchant_id' => $invoice->merchant_id,
            'invoice_no' => $invoice->invoice_no,
            'temp_journal_id' => $temp_journal->id,
            'debit_account_id' => $temp_journal->debit_account_id,
            'credit_account_id' => $temp_journal->credit_account_id,
            'amount' => $temp_journal->amount,
            'resourceable_id' => $temp_journal->resourceable_id,
            'resourceable_type' => $temp_journal->resourceable_type,
            'status' =>  $temp_journal->status,
            'thirdparty_invoice' =>  $temp_journal->thirdparty_invoice,
            'voucher_no' =>  $temp_journal->voucher_no,
            'pickup_date' => $temp_journal->pickup_date,
            'delivered_date' => $temp_journal->delivered_date,
            'receiver_name' =>  $temp_journal->receiver_name,
            'receiver_address' =>  $temp_journal->receiver_address,
            'receiver_phone' =>  $temp_journal->receiver_phone,
            'receiver_city' =>  $temp_journal->receiver_city,
            'receiver_zone' => $temp_journal->receiver_zone,
            'total_amount_to_collect' =>  $temp_journal->total_amount_to_collect,
            'voucher_remark' =>  $temp_journal->voucher_remark,
            'delivery_status_id' => $temp_journal->delivery_status_id,
            'delivery_status' => $temp_journal->delivery_status,
            'created_by' => auth()->user()->id,
        ]);
        
        if ($invoice_journal) {
            $temp_journal->status = 1;
            $temp_journal->save();

            if ($invoice->tax) {
                $tax_amount = $temp_journal->amount *  0.05;
            }else{
                $tax_amount = 0;
            }
            $invoice->total_amount = $invoice->total_amount + ($temp_journal->amount - $tax_amount);
            $invoice->total_voucher = $invoice->total_voucher + 1;
            $invoice->save();
        }

        return $invoice_journal->refresh();
    }

    public function upload(Invoice $invoice, array $data) : Attachment
    {
        $file = $data['file'];
        $file_name = null;
        $folder  = 'invoice';
        $date_folder = date('F-Y');
        $path = $folder . '/' . $date_folder;
        if ($file != "") {
            if (gettype($file) == 'string') {
                $file_name = $invoice->invoice_no . '_image_' . time() . '.' . 'png';
                Storage::disk('dospace')->put($path . '/' . $file_name, base64_decode($file));
            } else {
                $file_name = $invoice->invoice_no . '_image_' . time() . '_' . $file->getClientOriginalName();
                $content = file_get_contents($file);
                Storage::disk('dospace')->put($path . '/' . $file_name, $content);
            }
            Storage::setVisibility($path . '/' . $file_name, "public");

            return Attachment::create([
                'resource_type' => 'Invoice',
                'image' => $file_name,
                'resource_id' => $invoice->id,
                'note' => $invoice->note,
                'latitude' => null,
                'longitude' => null,
                'is_sign' => 0,
                'created_by' => auth()->user()->id
            ]);
        }
    }

    /**
     * @param Invoice $invoice
     */
    public function destroy(Invoice $invoice)
    {
        $deleted = $this->deleteById($invoice->id);

        if ($deleted) {
            $invoice->deleted_by = auth()->user()->id;
            $invoice->save();
        }
    }
}

// foreach ($temp_journals->chunk(500) as $temps) {
        //     foreach ($temps as $temp_journal) {
        //         $invoice_journal =  InvoiceJournal::create([
        //             'note' => $temp_journal->voucher_remark,
        //             'invoice_id' => $invoice->id,
        //             'merchant_id' => $invoice->merchant_id,
        //             'invoice_no' => $invoice->invoice_no,
        //             'temp_journal_id' => $temp_journal->id,
        //             'debit_account_id' => $temp_journal->debit_account_id,
        //             'credit_account_id' => $temp_journal->credit_account_id,
        //             'amount' => $temp_journal->amount,
        //             'resourceable_id' => $temp_journal->resourceable_id,
        //             'resourceable_type' => $temp_journal->resourceable_type,
        //             'status' => $temp_journal->status,
        //             'thirdparty_invoice' => $temp_journal->thirdparty_invoice,
        //             'voucher_no' => $temp_journal->voucher_no,
        //             'pickup_date' => $temp_journal->pickup_date,
        //             'delivered_date' => $temp_journal->delivered_date,
        //             'receiver_name' => $temp_journal->receiver_name,
        //             'receiver_address' => $temp_journal->receiver_address,
        //             'receiver_phone' => $temp_journal->receiver_phone,
        //             'receiver_city' => $temp_journal->receiver_city,
        //             'receiver_zone' => $temp_journal->receiver_zone,
        //             'total_amount_to_collect' => $temp_journal->total_amount_to_collect,
        //             'voucher_remark' => $temp_journal->voucher_remark,
        //             'created_by' => auth()->user()->id,
        //         ]);
            
        //         if ($invoice_journal) {
        //             array_push($tempIds, $temp_journal->id);
        //             // $temp_journal->status = 1;
        //             // $temp_journal->save();
        //             $total_amount += $temp_journal->amount;
        //         }
               
        //     }
        // }