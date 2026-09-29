<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket\Ticket;
use App\Models\Customer;

class AIConversation extends Model
{
    use HasFactory;

    protected $table = 'ai_conversations';

    protected $fillable = ['cust_id', 'session_id', 'status', 'ticket_id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'cust_id');
    }

    public function messages()
    {
        return $this->hasMany(AIMessage::class, 'conversation_id');
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
