<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    public function createdCourses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    // student courses
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_user', 'student_id', 'course_id');
    }

    // student courses
    public function enrollCourse(Course $course)
    {
        return $this->enrolledCourses()->attach($course);
    }

    // student courses
    public function unenrollCourse(Course $course)
    {
        return $this->enrolledCourses()->detach($course);
    }

    public function toArray()
    {
        $array = parent::toArray();

        // Add custom attributes to the array
        $array['role'] = $this->getRoleNames();
        // Only include created courses if the user is an instructor
        if ($this->hasRole('instructor')) {
            $array['created_courses'] = $this->createdCourses;
        }
        $array['enrolled_courses'] = $this->enrolledCourses;

        return $array;
    }
}
