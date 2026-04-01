<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'personnel' => new PersonnelResource($this->whenLoaded('personnel')),
            'log_date' => $this->log_date->toDateString(),
            'time_in' => $this->time_in ? $this->time_in->toTimeString() : null,
            'time_out' => $this->time_out ? $this->time_out->toTimeString() : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
