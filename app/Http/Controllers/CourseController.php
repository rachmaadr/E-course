<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Category;
use App\Models\course;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user = Auth::user();
        $query = Course::with(['category', 'teacher', 'students'])->orderByDesc('id');

        if ($user->hasRole('teacher')) {
            # code...
            $query->whereHas('teacher', function($query) use ($user){
                $query->where('user_id', $user->id);
            });
        }
        $courses = $query->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $categories = Category::all();
        return view('admin.courses.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        //
        $teacher = Teacher::where('user_id', Auth::user()->id)->first();
        
        if (!$teacher) {
            # code...
            return redirect()->route('admin.courses.index')->withErrors('Unauthorized or Invalid teacher');
        }

        DB::transaction(function() use ($request, $teacher){
            $validated = $request->validated();

            if ($request->hasFile('thumbnail')) {
                # code...
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }

            $validated['slug'] = Str::slug($validated['name']);
            $validated['teacher_id'] = $teacher->id;

            $course = Course::create($validated);

            if (!empty($validated['course_keypoints'])) {
                # code...
                foreach($validated['course_keypoints'] as $keypointText){
                    $course->course_keypoints()->create([
                        'name' => $keypointText,
                    ]);
                }
            }
        });

        return redirect()->route('admin.courses.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(course $course)
    {
        //
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(course $course)
    {
        //
        $categories = Category::all();
        return view('admin.courses.edit', compact('course', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, course $course)
    {
        //
        DB::transaction(function() use ($request, $course){
            $validated = $request->validated();

            if ($request->hasFile('thumbnail')) {
                # code...
                $thumbnailPath = $request->file('thumbnail')->store('thumbnail', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }

            $validated['slug'] = Str::slug($validated['name']);

            $course->update($validated);

            if (!empty($validated['course_keypoints'])) {
                # code...
                $course->course_keypoints()->delete();
                foreach($validated['course_keypoints'] as $keypointText){
                    $course->course_keypoints()->create([
                        'name' => $keypointText,
                    ]);
                }
            }
        });
        
        return redirect()->route('admin.courses.show', $course);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(course $course)
    {
        //
        DB::beginTransaction();

        try {
            //code...
            $course->delete();
            DB::commit();
            return redirect()->route('admin.courses.index');
        } catch (\Exception $th) {
            //throw $th;
            DB::rollBack();

            return redirect()->route('admin.courses.index')->with('eror', 'Eror Course Not Found ');
        }
    }
}
