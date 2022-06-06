<?php

namespace App\Repositories\Mobile\Api\v1;

use App\Models\Attachment;
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

    public function upload_profile($merchant, $file)
    {
        $file_name = null;
        $folder  = 'merchant';
        $date_folder = date('F-Y');
        $path = $folder.'/'.$date_folder;
        $file = $file['file'];
        if ($file != "") {
            //Delete current image
            if ($merchant->attachments->count() > 0) {
                $attachment = $merchant->attachments[0];
                $date_path = $attachment->created_at->format('F-Y');
                Storage::disk('dospace')->delete('merchant/' . $date_path . '/' . $attachment->image);
                $deleted = $this->deleteById($attachment->id);
    
                if ($deleted) {
                    $attachment->deleted_by = 1;
                    $attachment->save();
                }
            }

            if (gettype($file) == 'string') {
                $file_name = $merchant->id. '_' . $merchant->name . '_image_' . time() . '.' . 'png';
                Storage::disk('dospace')->put($path . '/' . $file_name, base64_decode($file));
            } else {
                $file_name = $merchant->id. '_' . $merchant->name . '_image_' . time() . '_' . $file->getClientOriginalName();
                $content = file_get_contents($file);
                Storage::disk('dospace')->put($path . '/' . $file_name, $content);
            }
            Storage::setVisibility($path . '/' . $file_name, "public");
            return Attachment::create([
                'resource_type' => 'Merchant',
                'image' => $file_name,
                'resource_id' => $merchant->id,
                'note' => $merchant->name,
                'latitude' => null,
                'longitude' => null,
                'is_sign' => 0,
                'created_by' => 1
            ]);
        }
    }

    public function create_attachment($product, $data)
    {
        $file_name = null;
        $file = $data['file'];
        $folder  = 'product/large';
        $date_folder = date('F-Y');
        $path = $folder.'/'.$date_folder;
        $is_sign = 0;
        if ($file != "") { 
            if (gettype($file) == 'string') {
                $file_name = 'product' . $product->id.'_image_' . time() . '.' . 'png';
                Storage::disk('dospace')->put($path . '/' . $file_name, base64_decode($file));
            } else {        
                $file_name = 'product' . $product->id.'_image_' . time() . '_' . $file->getClientOriginalName();
                $content = file_get_contents($file);
                Storage::disk('dospace')->put($path . '/' . $file_name, $content);
            }
            Storage::setVisibility($path . '/' . $file_name, "public");

            if (!$is_sign) {
                $medium = 'product/medium/' . $date_folder;
                $small = 'product/small/' . $date_folder;
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
            return Attachment::create([
                'resource_type' => 'Product',
                'image' => $file_name,
                'resource_id' => $product->id,
                'note' => isset($data['note'])?$data['note']:null,
                'latitude' => null,
                'longitude' => null,
                'is_sign' => 0,
                'created_by' => 1
            ]);
        }
    }
    
     /**
     * @param Attachment $attachment
     */
    public function destroy(Attachment $attachment)
    {
        if ($attachment->resource_type == 'Voucher') {
            $date_path = $attachment->created_at->format('F-Y');
            Storage::disk('dospace')->delete('voucher/' . $date_path . '/' . $attachment->image);
            $deleted = $this->deleteById($attachment->id);

        }elseif($attachment->resource_type == 'Product'){
            $path = strtolower($attachment->resource_type);
            $date_path = $attachment->created_at->format('F-Y');
            
            Storage::disk('dospace')->delete($path .'/large' .'/'.  $date_path . '/'. $attachment->image);
            Storage::disk('dospace')->delete($path .'/medium' .'/'.  $date_path . '/'. $attachment->image);
            Storage::disk('dospace')->delete($path .'/small' .'/'.  $date_path . '/'. $attachment->image);

            $deleted = $this->deleteById($attachment->id);
        }

        if ($deleted) {
            $attachment->deleted_by = 1;
            $attachment->save();
        }
    }
}
