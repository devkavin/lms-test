<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\Sanctum;

class CourseControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    protected $user;
    protected $instructor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->instructor = User::factory()->create();
        Role::create(['name' => 'instructor']);
        Role::create(['name' => 'student']);

        $this->instructor->assignRole('instructor');
        $this->user->assignRole('student');

        Course::factory(3)->create([
            'instructor_id' => $this->instructor->id,
        ]);
    }

    public function test_creating_instructor_and_assigning_role()
    {
        $this->assertDatabaseHas('users', ['name' => $this->instructor->name]);
        $this->assertTrue($this->instructor->hasRole('instructor'));
    }

    public function test_creating_student_and_assigning_role()
    {
        $this->assertDatabaseHas('users', ['name' => $this->user->name]);
        $this->assertTrue($this->user->hasRole('student'));
    }

    public function test_instructor_can_create_course()
    {
        Sanctum::actingAs($this->instructor);

        $response = $this->postJson('/api/courses', [
            'title' => 'Test Course',
            'description' => 'Test Description',
            'category' => 'Test Category',
            'instructor_id' => $this->instructor->id,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('courses', ['title' => 'Test Course']);
    }

    public function test_student_cannot_create_course()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/courses', [
            'title' => 'Test Course',
            'description' => 'Test Description',
            'category' => 'Test Category',
            'instructor_id' => $this->instructor->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_getting_all_courses()
    {
        Sanctum::actingAs($this->instructor);

        $response = $this->getJson('/api/courses');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'courses');
    }

    public function test_getting_single_course()
    {
        Sanctum::actingAs($this->instructor);

        $course = Course::first();

        $response = $this->getJson('/api/courses/' . $course->id);

        $response->assertStatus(200)
            ->assertJson(['course' => $course->toArray()]);
    }

    public function test_instructor_can_update_course()
    {
        Sanctum::actingAs($this->instructor);

        $course = Course::first();

        $response = $this->putJson('/api/courses/' . $course->id, [
            'title' => 'Updated Course',
            'description' => 'Updated Description',
            'category' => 'Updated Category',
        ]);

        $response->assertStatus(200)
            ->assertJson(['course' => [
                'title' => 'Updated Course',
                'description' => 'Updated Description',
                'category' => 'Updated Category',
            ]]);
    }

    public function test_student_cannot_update_course()
    {
        Sanctum::actingAs($this->user);

        $course = Course::first();

        $response = $this->putJson('/api/courses/' . $course->id, [
            'title' => 'Updated Course',
            'description' => 'Updated Description',
            'category' => 'Updated Category',
        ]);

        $response->assertStatus(403);
    }

    public function test_instructor_can_delete_course()
    {
        Sanctum::actingAs($this->instructor);

        $course = Course::first();

        $response = $this->deleteJson('/api/courses/' . $course->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_student_cannot_delete_course()
    {
        Sanctum::actingAs($this->user);

        $course = Course::first();

        $response = $this->deleteJson('/api/courses/' . $course->id);

        $response->assertStatus(403);
    }
}
