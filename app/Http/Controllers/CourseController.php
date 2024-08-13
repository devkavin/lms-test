<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::all();

        return response()->json([
            'courses' => $courses
        ], 200);
    }

    public function show($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'message' => 'Course not found'
            ], 404); // not found
        }

        return response()->json([
            'course' => $course->with('students')->get()
        ], 200);
    }

    public function store(Request $request)
    {

        $user = Auth::user();

        $data = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'category' => 'required',
        ]);

        if (!$user->hasRole('instructor')) {
            return response()->json([
                'message' => 'You are not allowed to create a course'
            ], 403); // forbidden
        }

        $course = Course::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'category' => $data['category'],
            'instructor_id' => Auth::id()
        ]);

        return response()->json([
            'course' => $course
        ], 201); // 201 for succesfully creating a resource
    }


    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $course = Course::find($request->id);

        $data = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'category' => 'required',
        ]);

        if (!$course) {
            return response()->json([
                'message' => 'Course not found'
            ], 404); // not found
        }

        if (!$user->hasRole('instructor')) {
            return response()->json([
                'message' => 'You are not allowed to update a course'
            ], 403); // forbidden
        }

        $course->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'category' => $data['category'],
        ]);


        return response()->json([
            'course' => $course
        ], 200); // 200 for succesfully updating a resource
    }

    public function enroll($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response([
                'message' => "Course not found"
            ], 404);
        }

        if ($course->instructor_id === Auth::id()) {
            return response([
                'message' => 'You can\'t enroll in your own course'
            ], 403);
        }

        if ($course->students->contains(Auth::id())) {
            return response([
                'message' => 'You are already enrolled in this course'
            ], 403);
        }

        Auth::user()->enrollCourse($course);

        return response([
            'message' => 'You have enrolled in the course'
        ], 200);
    }

    public function unenroll($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response([
                'message' => "Course not found"
            ], 404);
        }

        if ($course->instructor_id === Auth::id()) {
            return response([
                'message' => 'You can\'t unenroll from your own course'
            ], 403);
        }

        if (!$course->students->contains(Auth::id())) {
            return response([
                'message' => 'You are not enrolled in this course'
            ], 403);
        }

        Auth::user()->unenrollCourse($course);

        return response([
            'message' => 'You have unenrolled from the course'
        ], 200);
    }

    // is enrolled
    public function isEnrolled($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response([
                'message' => "Course not found"
            ], 404);
        }

        if ($course->students->contains(Auth::id())) {
            return response([
                'is_enrolled' => true
            ], 200);
        }

        return response([
            'is_enrolled' => false
        ], 200);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $course = Course::find($id);

        if (!$course) {
            return response([
                'message' => "Course not found"
            ], 404);
        }

        if (!$user->hasRole('instructor')) {
            return response()->json([
                'message' => 'You are not allowed to delete a course'
            ], 403); // forbidden
        }

        // detach all students before deleting the course(cannot delete because of pivot table relationship)
        $course->students()->detach();

        $course->delete();

        return response([
            'message' => 'Course Deleted'
        ], 204); // 204 for succesfully deleting a resource (no content)
    }
}
