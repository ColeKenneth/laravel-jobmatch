<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enum\UserRole;
use App\Enum\YearLevel;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'role',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'full_name'
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
            'role' => UserRole::class,
            'is_active' => 'boolean'
        ];
    }

    public function getFullNameAttribute() : string
    {
        return trim($this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name);
    }

    public function student() : HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function alumni() : HasOne
    {
        return $this->hasOne(Alumni::class);
    }

    public function employer() : HasOne
    {
        return $this->hasOne(Employer::class);
    }

    protected static function booted(): void
    {
        static::created(function (User $user) {
            match ($user->role) {
                UserRole::ALUMNI => Alumni::create([
                    'user_id' => $user->id,
                    'program' => 'Not specified',
                    'graduation_date' => now()->addYear(),
                ]),

                UserRole::STUDENT => Student::create([
                    'user_id' => $user->id,
                    'student_id_number' => date('y') . '-QC' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT),
                    'program' => 'Not specified',
                    'year_level' => YearLevel::FOURTH_YEAR->value,
                    'gwa' => 0.00,
                ]),

                UserRole::EMPLOYER => Employer::create([
                    'user_id' => $user->id,
                    'company_name' => 'Not specified',
                    'company_description' => 'Not specified',
                    'industry' => 'Not specified',
                    'logo_url' => 'https://via.placeholder.com/150'
                ]),

                default => null,
            };
        });
    }
}
