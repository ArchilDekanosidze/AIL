<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    use HasFactory;

    protected $table = 'chat_message_attachments'; // ✅ Fixes the table name

    protected $fillable = [
        'message_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size', 
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
