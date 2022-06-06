<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Invoice;
use App\Models\TempJournal;
use Illuminate\Http\Request;
use App\Exports\InvoiceSheet;
use Illuminate\Http\Response;
use App\Models\InvoiceJournal;
use App\Exports\InvoiceDetailSheet;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Invoice\UploadRequest;
use App\Http\Resources\Invoice\InvoiceResource;
use App\Http\Requests\Invoice\AddInvoiceRequest;
use App\Http\Resources\Invoice\InvoiceCollection;
use App\Http\Requests\Invoice\UpdateAmountRequest;
use App\Repositories\Web\Api\v1\InvoiceRepository;
use App\Http\Requests\Invoice\CreateInvoiceRequest;
use App\Http\Requests\Invoice\RemoveInvoiceRequest;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Http\Resources\InvoiceHistory\InvoiceHistoryResource;
use App\Http\Resources\InvoiceJournal\InvoiceJournalResource;
use App\Http\Resources\InvoiceHistory\InvoiceHistoryCollection;

class InvoiceController extends Controller
{
    protected $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function index()
    {
        //export invoice
        if (request()->has('export') && request()->get('export')==true) {
            $filename = 'invoice.xlsx';
            Excel::store(new InvoiceSheet, $filename, 'public', null, [
                    'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/invoice.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }
        //get $export lists
        $invoices =  Invoice::with('merchant')->filter(request()->only([
                                'invoice_no','start_date', 'end_date','merchant_id'
                            ]))
                            ->where('city_id', auth()->user()->city_id)
                            ->orderBy('id', 'desc');
        // get export list with paginate or not paginate
        if (request()->get('paginate') && is_numeric(request()->get('paginate'))) {
            $paginate_count = request()->get('paginate') ? request()->get('paginate') : 25;
            $invoices = $invoices->paginate($paginate_count);
        } else {
            $invoices = $invoices->get();
        }

        return new InvoiceCollection($invoices);
    }

    public function store(CreateInvoiceRequest $request)
    {
        if ($request->get('temp_journals')) {
            $request->merge([
                'temp_journals' => array_unique($request->get('temp_journals'), SORT_REGULAR)
            ]);
        }

        $invoice = $this->invoiceRepository->create($request->all());
        return new InvoiceResource($invoice->load(['merchant']));
    }

    public function show(Invoice $invoice)
    {
        //export invoice
        if (request()->has('export') && request()->get('export')==true) {
            $filename = 'invoice'.$invoice->invoice_no.'.xlsx';
            Excel::store(new InvoiceDetailSheet($invoice->id), $filename, 'public', null, [
                    'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/invoice'.$invoice->invoice_no.'.xlsx');
            return response()->download($file);
        }
        return new InvoiceResource($invoice->load(['merchant', 'invoice_journals', 'attachments']));
    }

    public function update(Invoice $invoice, Request $request)
    {
        if (!$invoice->payment_status) {
            $invoice = $this->invoiceRepository->update($invoice, $request->all());

            return new InvoiceResource($invoice);
        }

        return response()->json([
            'status' => 2, 'message' => 'Invoice is already confirm.'
        ], Response::HTTP_OK);
    }

    public function confirm(Invoice $invoice)
    {
        if (!$invoice->payment_status) {
            $invoice = $this->invoiceRepository->confirm($invoice);
            return new InvoiceResource($invoice->load([
                'merchant', 'invoice_journals', 'attachments'
            ]));
        }

        return response()->json([
            'status' => 2, 'message' => 'Invoice is already confirm.'
        ], Response::HTTP_OK);
    }

    public function upload(Invoice $invoice, UploadRequest $request)
    {
        $attachment = $this->invoiceRepository->upload($invoice, $request->all());

        if ($attachment) {
            return new AttachmentResource($attachment);
        } else {
            return response()->json([
                'status' => 2, 'message' => 'Upload Fail'
            ], Response::HTTP_OK);
        }
    }

    public function update_adjustment_amount(InvoiceJournal $invoice_journal, UpdateAmountRequest $request)
    {  
        $invoice = $this->invoiceRepository->update_adjustment_amount($invoice_journal, $request->all());
        return new InvoiceJournalResource($invoice);
    }

    public function removeVoucher(Invoice $invoice, RemoveInvoiceRequest $request)
    {
        if (!$invoice->payment_status) {
            $invoice = $this->invoiceRepository->remove_voucher($invoice, $request->all());

            return response()->json([
                'status' => $invoice['status'], 'message' => $invoice['message']
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => 2, 'message' => 'Cannot remove because invoice is already closed'
        ], Response::HTTP_OK);
    }
    
    public function addVoucher(Invoice $invoice, AddInvoiceRequest $request)
    {
        if (!$invoice->payment_status) {
            $temp_journal = TempJournal::where('voucher_no', $request['temp_journal_id'])
                                        ->orWhere('thirdparty_invoice',$request['temp_journal_id'])
                                        ->firstOrFail();
            if ($temp_journal->status) {
                return response()->json([
                    'status' => 2, 'message' => 'Cannot add because this voucher no is already include in other invoice'
                ], Response::HTTP_OK);
            }
            $invoice = $this->invoiceRepository->add_voucher($invoice, $temp_journal);

            return new InvoiceJournalResource($invoice);
        }

        return response()->json([
            'status' => 2, 'message' => 'Cannot remove because invoice is already closed'
        ], Response::HTTP_OK);
    }

    public function histories(Invoice $invoice)
    {
        return new InvoiceHistoryCollection($invoice->invoice_histories);
    }
    
    public function destroy(Invoice $invoice)
    {
        if ($invoice->payment_status || $invoice->invoice_journals->count() > 0) {
            return response()->json([
                'status' => 2, 'message' => 'Cannot delete'
            ], Response::HTTP_OK);
       }
        $this->invoiceRepository->destroy($invoice);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
