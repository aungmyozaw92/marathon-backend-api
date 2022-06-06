<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Message;
use App\Models\Voucher;
use App\Repositories\BaseRepository;

class VoucherMessageRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Message::class;
    }

    /**
     * @param array $data
     *
     * @return Message
     */
    public function create(array $data): Message
    {
        $voucher = Voucher::findOrFail(request()->route('voucher'));
        $message_text = getConvertedString($data['message_text']);

        $message = $voucher->messages()->create([
            'staff_id' => auth()->user()->id,
            'message_text' => $message_text
        ]);
        auth()->user()->messages()->save($message);
        return $message;
    }

    /**
     * @param Message  $message
     * @param array $data
     *
     * @return mixed
     */
    public function update(Message $message, array $data): Message
    {
        if (isset($data['message_text'])) {
            $message_text = getConvertedString($data['message_text']);
        }

        $message->message_text = isset($data['message_text']) ? $message_text : $message->message_text;

        if ($message->isDirty()) {
            $message->staff_id = auth()->user()->id;
            $message->save();
        }

        return $message->refresh();
    }

    /**
     * @param Message $message
     */
    public function destroy(Message $message)
    {
        $deleted = $this->deleteById($message->id);

        // if ($deleted) {
        //     $message->deleted_by = auth()->user()->id;
        //     $message->save();
        // }
    }
}
