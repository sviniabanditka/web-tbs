<?php

namespace App\Modules\Common\Models;

use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'username',
        'email',
    ];

    protected $hidden = [
        'remember_token',
    ];

    public function loginCodes(): HasMany
    {
        return $this->hasMany(\App\Modules\Auth\Models\LoginCode::class);
    }

    public static function generateUsername(string $email): string
    {
        $baseUsername = Str::before($email, '@');
        $cleanUsername = preg_replace('/[^a-zA-Z0-9_]/', '', $baseUsername);
        $username = $cleanUsername;
        $counter = 1;

        while (static::where('username', $username)->exists()) {
            $username = $cleanUsername . '_' . $counter;
            $counter++;
        }

        return $username;
    }
}
