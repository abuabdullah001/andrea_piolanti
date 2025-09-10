<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable implements JWTSubject
{

    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'avatar',
        'name',
        'username',
        'email',
        'password',
        'phone',
        'email_verified_at',
        'about_me',
        'description',
        'address',
        'map_link',
        'status',
        'is_admin',
        'otp',
        'otp_created_at',
        'preffered_contact',
        'communication',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Implement JWTSubject methods
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Typically the user ID
    }

    public function getJWTCustomClaims()
    {
        return []; // Add any custom claims here
    }

    // Relationships
    public function favourites()
    {
        return $this->belongsToMany(Service::class, 'favourites', 'user_id', 'service_id');
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }
    public function services()
    {
        return $this->hasMany(Service::class, 'owner_id');
    }
    public function images()
    {
        return $this->hasMany(UserImage::class, 'user_id');
    }
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
    public function likedReviews()
    {
        return $this->belongsToMany(Review::class, 'review_likes')->withTimestamps();
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }
}
