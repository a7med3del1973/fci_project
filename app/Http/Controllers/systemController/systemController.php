<?php

namespace App\Http\Controllers\systemController;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Degree;
use App\Models\isCourseRegisterActive;
use App\Models\Lecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class systemController extends Controller
{
    public function login(Request $request)
    {

        $credentials = $request->only('email', 'password', 'role_id');

        if (Auth::attempt($credentials)) {
            $role = Auth::user()->role->role_name;
            $isActive = isCourseRegisterActive::where('isActive', 1)->first();
            session()->put('courseActive', $isActive != null? 1 : 0);
            if ($role == "Admin") {
                return response()->json([
                    'message' => 'Login successful',
                    'redirect' => '/admin/dashboard'
                ]);
            }else if ($role == "Doctor") {
                return response()->json([
                    'message' => 'Login successful',
                    'redirect' => '/professor/dashboard'
                ]);
            }else if ($role == "Student") {
                return response()->json([
                    'message' => 'Login successful',
                    'redirect' => '/student/dashboard'
                ]);
            }
        }

        return response()->json([
            'message' => 'Incorrect email or password'
        ],
         401);
    }

    public function details($courseId)
    {
        $course = Course::find($courseId);
        $pre = $course->prerequisites;
        $degree = Degree::where('student_id', Auth::user()->id)->where('course_id', $courseId)->get();
        return View('courseDetails')->with([
            'user_id' => Auth::user()->id,
            'course' => $course,
            'pre' => $pre,
            'degree' => $degree
        ]);
    }

    public function lectures($id)
    {
        $lectures = Lecture::where('course_id', $id)->get();
        return View('lectures')->with([
            'lectures' => $lectures,
            'course id' => $id,
        ]);
    }

    public function assignments(Request $request, $id)
    {
        $assignments = Assignment::where('course_id', $id)->get();
        return response()->json([
            'assignments' => $assignments
        ]);
        return View('assignment')->with([
            'assignments' => $assignments,
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
