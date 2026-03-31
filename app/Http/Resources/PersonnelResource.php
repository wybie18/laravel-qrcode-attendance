<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonnelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'qr_code' => $this->qr_code,
            'office_id' => $this->office_id,
            'position_id' => $this->position_id,
            'office' => new OfficeResource($this->whenLoaded('office')),
            'position' => new PositionResource($this->whenLoaded('position')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
