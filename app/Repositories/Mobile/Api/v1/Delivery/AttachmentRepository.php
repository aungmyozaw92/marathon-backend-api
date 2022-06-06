<?php

namespace App\Repositories\Mobile\Api\v1\Delivery;

use Rabbit;
use App\Models\Voucher;
use App\Models\Waybill;
use App\Models\Attachment;
use App\Models\ReturnSheet;
use App\Repositories\BaseRepository;
use Intervention\Image\Facades\Image;
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

    /**
     * @param array $data
     *
     * @return Attachment
     */
    public function create(array $data)
    {
        $file_name = null;
        if (isset($data['voucher_id']) && $data['voucher_id']) {
            $voucher = Voucher::findOrFail($data['voucher_id']);
            $invoice_no = $voucher->voucher_invoice;
            $resource_type = 'Voucher';
            $resource_id = $voucher->id;
        } elseif (isset($data['waybill_id']) && $data['waybill_id']) {
            $waybill = Waybill::findOrFail($data['waybill_id']);
            $invoice_no = $waybill->waybill_invoice;
            $resource_type = 'Waybill';
            $resource_id = $waybill->id;
        } elseif (isset($data['return_sheet_id']) && $data['return_sheet_id']) {
            $return_sheet = ReturnSheet::findOrFail($data['return_sheet_id']);
            $invoice_no = $return_sheet->return_sheet_invoice;
            $resource_type = 'ReturnSheet';
            $resource_id = $return_sheet->id;
        }
        $is_sign = isset($data['is_sign']) ? $data['is_sign'] : 0;
        $folder  = ($is_sign)?'singature':'large';

        $date_folder = date('F-Y');
        $path = $folder.'/'.$date_folder;
        if (isset($data['file']) && $data['file']) {
            $file_data = $data['file'];
            //Resize Image
            $medium = 'medium/' . $date_folder;
            $small = 'small/' . $date_folder;
            if ($file_data != "") {
                if (gettype($data['file']) == 'string') {
                    $file_name = $invoice_no . '_image_' . time() . '.' . 'png';
                    Storage::disk('dospace')->put($path . '/' . $file_name, base64_decode($file_data));
                } else {
                    $file_name = $invoice_no . '_image_' . time() . '_' . $file_data->getClientOriginalName();
                    $content = file_get_contents($file_data);
                    Storage::disk('dospace')->put($path . '/' . $file_name, $content);
                }
                Storage::setVisibility($path . '/' . $file_name, "public");

                if (!$is_sign) {
                    //small image save
                    $small_img = Image::make($content)->resize(250, 250, function ($constraint) {
                        $constraint->aspectRatio();
                    })->stream('jpg', 100);

                    Storage::disk('dospace')->put($small . '/' . $file_name, $small_img);
                    Storage::setVisibility($small . '/' . $file_name, "public");

                    $file = $data['file'];
                    list($width, $height) = getimagesize($file);

                    //small image save
                    $medium_img = Image::make($content)->resize($width/2, $height/2, function ($constraint) {
                        $constraint->aspectRatio();
                    })->stream('jpg', 100);

                    Storage::disk('dospace')->put($medium . '/' . $file_name, $medium_img);
                    Storage::setVisibility($medium . '/' . $file_name, "public");
                }
                // Storage::disk('dospace')->putFile('large', request()->file, 'public');
            }
            if (isset($data['note'])) {
                $note = getConvertedString($data['note']);
            }
           
            return Attachment::create([
                'resource_type' => $resource_type,
                'image' => $file_name,
                'resource_id' => $resource_id,
                'note' => isset($data['note']) ? $note : null,
                'latitude' => isset($data['latitude']) ? $data['latitude'] : null,
                'longitude' => isset($data['longitude']) ? $data['longitude'] : null,
                'is_sign' => $is_sign,
                'created_by' => auth()->user()->id
            ]);
        }

        return true;
    }

    public function create_pickup_attachmet($pickup, $data)
    {
        $file_name = null;
        $file = $data['file'];
        $folder  = 'pickup';
        $date_folder = date('F-Y');
        $path = $folder.'/'.$date_folder;
        if ($file != "") { 
            if (gettype($file) == 'string') {
                $file_name = 'pickup' . $pickup->id.'_image_' . time() . '.' . 'png';
                Storage::disk('dospace')->put($path . '/' . $file_name, base64_decode($file));
            } else {        
                $file_name = 'pickup' . $pickup->id.'_image_' . time() . '_' . $file->getClientOriginalName();
                $content = file_get_contents($file);
                Storage::disk('dospace')->put($path . '/' . $file_name, $content);
            }
            Storage::setVisibility($path . '/' . $file_name, "public");
            return Attachment::create([
                'resource_type' => 'Pickup',
                'image' => $file_name,
                'resource_id' => $pickup->id,
                'note' => isset($data['note'])?$data['note']:null,
                'latitude' => null,
                'longitude' => null,
                'is_sign' => 0,
                'created_by' => auth()->user()->id
            ]);
        }
    }
    
     /**
     * @param Attachment $attachment
     */
    public function destroy(Attachment $attachment)
    {
        $path = strtolower($attachment->resource_type);
        $date_path = $attachment->created_at->format('F-Y');
        
        Storage::disk('dospace')->delete($path .'/'. $date_path . '/'. $attachment->image);

        $deleted = $this->deleteById($attachment->id);

        if ($deleted) {
            $attachment->deleted_by = auth()->user()->id;
            $attachment->save();
        }
    }
    /* public function create(array $data): Attachment
    {
        $file_name = null;
        if (isset($data['voucher_id']) && $data['voucher_id']) {
            $voucher = Voucher::findOrFail($data['voucher_id']);
            $invoice_no = $voucher->voucher_invoice;
            $resource_type = 'Voucher';
            $resource_id = $voucher->id;
        } else {
            $waybill = Waybill::findOrFail($data['waybill_id']);
            $invoice_no = $waybill->waybill_invoice;
            $resource_type = 'Waybill';
            $resource_id = $waybill->id;
        }

        $is_sign = isset($data['is_sign']) ? $data['is_sign'] : 0;
        $folder  = ($is_sign)?'singature':'large';

        $date_folder = date('F-Y');
        $path = $folder.'/'.$date_folder;

        if (isset($data['file']) && $data['file']) {
            $file_data = $data['file'];
            //Resize Image
            $medium = 'medium/' . $date_folder;
            //generating unique file name;
            $file_name = $invoice_no . '_image_' . time() . '.png';
            if ($file_data != "") {
                // storing image in storage/app/public Folder
                Storage::disk('dospace')->put($path.'/' . $file_name, base64_decode($file_data));
                Storage::setVisibility($path . '/' . $file_name, "public");

                if (!$is_sign) {
                    $img = Image::make(base64_decode($file_data))->resize(250, 250)->stream('jpg', 100);

                    Storage::disk('dospace')->put($medium . '/' . $file_name, $img);
                    Storage::setVisibility($medium . '/' . $file_name, "public");
                }
                // Storage::disk('dospace')->putFile('large', request()->file, 'public');
            }
        }

        return Attachment::create([
            'resource_type' => $resource_type,
            'image' => $file_name,
            'resource_id' => $resource_id,
            'note' => isset($data['note']) ? $data['note'] : null,
            'latitude' => isset($data['latitude']) ? $data['latitude'] : null,
            'longitude' => isset($data['longitude']) ? $data['longitude'] : null,
            'is_sign' => $is_sign,
            'created_by' => auth()->user()->id
        ]);
    } */
}
