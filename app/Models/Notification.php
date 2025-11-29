<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $guarded = ['id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'notification_user')
                    ->withPivot('read_at', 'deleted_at')
                    ->withTimestamps();
    }
    
    public function getReadAtAttribute()
    {
        $pivot = $this->users->where('id', Auth::id())->first();
        return $pivot ? $pivot->pivot->read_at : null;
    }

    public static function send($userId, $title, $message, $type = 'system')
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => false,
        ]);
    }
}
