<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket\Ticket;
Use App\Models\User;
Use App\Models\Customer;

class AIConversation extends Model
{
    use HasFactory;

    protected $table = 'ai_conversations';
    
    protected $fillable = ['cust_id', 'session_id', 'status'];

    public function customer()
    {
        return $this->belongsTo(User::class, 'cust_id');
    }

    public function messages()
    {
        return $this->hasMany(AIMessage::class);
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class); // opsional, jika ticket dibuat dari AI
    }
}
