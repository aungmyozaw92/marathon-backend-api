<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Attachment;
use Illuminate\Support\Str;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Storage;

class AttachmentRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Attachment::class;
    }

    public function create_attachment($transaction, $file)
    {
        $file_name = null;
        $folder  = 'transaction';
        $date_folder = date('F-Y');
        $path = $folder . '/' . $date_folder;
        if ($file != "") {
            if (gettype($file) == 'string') {
                $file_name = $transaction->transaction_no . '_image_' . time() . '.' . 'png';
                Storage::disk('dospace')->put($path . '/' . $file_name, base64_decode($file));
            } else {
                $file_name = $transaction->transaction_no . '_image_' . time() . '_' . $file->getClientOriginalName();
                $content = file_get_contents($file);
                Storage::disk('dospace')->put($path . '/' . $file_name, $content);
            }
            Storage::setVisibility($path . '/' . $file_name, "public");

            return Attachment::create([
                'resource_type' => 'Transaction',
                'image' => $file_name,
                'resource_id' => $transaction->id,
                'note' => $transaction->note,
                'latitude' => null,
                'longitude' => null,
                'is_sign' => 0,
                'created_by' => auth()->user()->id
            ]);
        }
    }

    public function create_deliSheet_attachmet($deliSheet, $data)
    {
        $file_name = null;
        $file = $data['file'];
        $folder  = 'deli_sheet';
        $date_folder = date('F-Y');
        $path = $folder.'/'.$date_folder;
        if ($file != "") {
            if (gettype($file) == 'string') {
                $file_name = 'deli_sheet' . $deliSheet->id.'_image_' . time() . '.' . 'png';
                Storage::disk('dospace')->put($path . '/' . $file_name, base64_decode($file));
            } else {
                $file_name = 'deli_sheet' . $deliSheet->id.'_image_' . time() . '_' . $file->getClientOriginalName();
                $content = file_get_contents($file);
                Storage::disk('dospace')->put($path . '/' . $file_name, $content);
            }
            Storage::setVisibility($path . '/' . $file_name, "public");
            return Attachment::create([
                'resource_type' => 'DeliSheet',
                'image' => $file_name,
                'resource_id' => $deliSheet->id,
                'note' => isset($data['note']) ? $data['note'] : null,
                'latitude' => null,
                'longitude' => null,
                'is_sign' => 0,
                'created_by' => auth()->user()->id
            ]);
        }
    }

    public function create_waybill_attachmet($waybill, $data)
    {
        $file_name = null;
        $file = $data['file'];
        $folder  = 'large';
        $date_folder = date('F-Y');
        $path = $folder.'/'.$date_folder;
        if ($file != "") {
            if (gettype($file) == 'string') {
                $file_name = 'large' . $waybill->id.'_image_' . time() . '.' . 'png';
                Storage::disk('dospace')->put($path . '/' . $file_name, base64_decode($file));
            } else {
                $file_name = 'large' . $waybill->id.'_image_' . time() . '_' . $file->getClientOriginalName();
                $content = file_get_contents($file);
                Storage::disk('dospace')->put($path . '/' . $file_name, $content);
            }
            Storage::setVisibility($path . '/' . $file_name, "public");
            return Attachment::create([
                'resource_type' => 'Waybill',
                'image' => $file_name,
                'resource_id' => $waybill->id,
                'note' => isset($data['note']) ? $data['note'] : null,
                'latitude' => null,
                'longitude' => null,
                'is_sign' => 0,
                'created_by' => auth()->user()->id
            ]);
        }
    }

    public function destroy(Attachment $attachment)
    {
        // type of resources - 'Transaction', 'ReturnSheet', 'Pickup', 'Voucher', 'Waybill' , 'DeliSheet'
        $date_path = $attachment->created_at->format('F-Y');
        Storage::disk('dospace')->delete(Str::snake($attachment->resource_type) . '/' . $date_path . '/' . $attachment->image);
        $deleted = $this->deleteById($attachment->id);
        if ($deleted) {
            $attachment->deleted_by = auth()->user()->id;
            $attachment->save();
        }
    }

    public function finance_attachment($resource, $folder, $prefix, $file)
    {
        $file_name = null;
        $date_folder = date('F-Y');
        $path = $folder . '/' . $date_folder;
        if ($file != "") {
            if (gettype($file) == 'string') {
                $file_name = $resource->$prefix . '_image_' . time() . '.' . 'png';
                Storage::disk('dospace')->put($path . '/' . $file_name, base64_decode($file));
            } else {
                $file_name = $resource->$prefix . '_image_' . time() . '_' . $file->getClientOriginalName();
                $content = file_get_contents($file);
                Storage::disk('dospace')->put($path . '/' . $file_name, $content);
            }
            Storage::setVisibility($path . '/' . $file_name, "public");
            \Log::info(class_basename($resource));
            return Attachment::create([
                'resource_type' => class_basename($resource),
                'image' => $file_name,
                'resource_id' => $resource->id,
                'note' => $resource->$prefix,
                'latitude' => null,
                'longitude' => null,
                'is_sign' => 0,
                'created_by' => auth()->user()->id
            ]);
        }
    }
}
