<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'conversation_id' => $this->conversation_id,
            'user_id' => $this->user_id,
            'content' => $this->content,
            'type' => $this->type,
            'file_url' => $this->file_url,
            'file_name' => $this->file_name,
            'file_size' => $this->file_size,
            'is_edited' => $this->is_edited,
            'edited_at' => $this->edited_at?->toISOString(),
            'is_deleted' => $this->is_deleted,
            'deleted_at' => $this->deleted_at?->toISOString(),
            'user' => new UserResource($this->whenLoaded('user')),
            'conversation' => new ConversationResource($this->whenLoaded('conversation')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
