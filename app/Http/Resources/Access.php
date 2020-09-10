<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Access extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'rfid_id' => $this->rfid_id,
        ];
    }
}
