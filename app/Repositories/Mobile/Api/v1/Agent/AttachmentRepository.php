<?php

namespace App\Repositories\Mobile\Api\v1\Agent;

use App\Models\Attachment;
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

    public function create_attachmet($agent, $file)
    {
        $file_name = null;
        $path  = 'agent';
        $file = $file['file'];
        if ($file != "") { 
            //Delete current image
            if ($agent->attachments->count() > 0) {
                $attachment = $agent->attachments[0];
                
                Storage::disk('dospace')->delete('agent/' . $attachment->image);
                $deleted = $this->deleteById($attachment->id);
    
                if ($deleted) {
                    $attachment->deleted_by = 1;
                    $attachment->save();
                }
            }

            if (gettype($file) == 'string') {
                $file_name = $agent->id. '_' . $agent->name . '_image_' . time() . '.' . 'png';
                Storage::disk('dospace')->put($path . '/' . $file_name, base64_decode($file));
            } else {        
                $file_name = $agent->id. '_' . $agent->name . '_image_' . time() . '_' . $file->getClientOriginalName();
                $content = file_get_contents($file);
                Storage::disk('dospace')->put($path . '/' . $file_name, $content);
            }
            Storage::setVisibility($path . '/' . $file_name, "public");
            return Attachment::create([
                'resource_type' => 'Agent',
                'image' => $file_name,
                'resource_id' => $agent->id,
                'note' => $agent->name,
                'latitude' => null,
                'longitude' => null,
                'is_sign' => 0,
                'created_by' => 1
            ]);
        }
    }

    // public function destroy(Attachment $attachment)
    // {
    //     if ($attachment->resource_type == 'Transaction') {
    //         $date_path = $attachment->created_at->format('F-Y');
    //          Storage::disk('dospace')->delete('transaction/' . $date_path . '/' . $attachment->image);
    //          $deleted = $this->deleteById($attachment->id);
    //         if ($deleted) {
    //             $attachment->deleted_by = auth()->user()->id;
    //             $attachment->save();
    //         }
    //     }
        
    // }
}
