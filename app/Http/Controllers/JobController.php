<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = Job::latest()->get()->groupBy('featured');

        return view('jobs.index', [
            'jobs' => $jobs[0],
            'featuredJobs' => $jobs[1],
            'tags' => Tag::all(),
        ]);
    }

    public function allTags()
    {
        $jobs = Job::latest()->get()->groupBy('featured');

        return view('jobs.allTags', [
            'jobs' => $jobs[0],
            'featuredJobs' => $jobs[1],
            'tags' => Tag::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('jobs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'title'    => ['required'],
            'salary'   => ['required'],
            'location' => ['required'],
            'schedule' => ['required', Rule::in(['Part Time', 'Full Time'])],
            'url'      => ['required', 'active_url'],
            'tags'     => ['nullable'],
        ]);

        $attributes['featured'] = $request->has('featured');

        $job = Auth::user()->employer->jobs()->create(Arr::except($attributes, 'tags'));

        if ($attributes['tags'] ?? false) {
            foreach (explode(',', $attributes['tags']) as $tag) {
                $job->tag($tag);
            }
        }

        return redirect('/jobs');
    }

    public function import(Request $request)
        {
            $jobs = $request->input('jobs', []);

            if (empty($jobs)) {
                return response()->json([
                    'message' => '⚠️ Nema podataka za import!',
                ], 422);
            }

            foreach ($jobs as $jobData) {
                // Ako nema vrijednosti → popuni default
                $attributes = [
                    'title'    => $jobData['title']    ?? 'Untitled Job',
                    'salary'   => $jobData['salary']   ?? 'Not specified',
                    'location' => $jobData['location'] ?? 'Unknown',
                    'schedule' => $jobData['schedule'] ?? 'Full Time',
                    'url'      => $jobData['url']      ?? 'https://example.com',
                    'featured' => $jobData['featured'] ?? false,
                ];

                $job = Auth::user()->employer->jobs()->create($attributes);

                // Ako ima tagova → dodaj ih
                if (!empty($jobData['tags'])) {
                    foreach (explode(',', $jobData['tags']) as $tag) {
                        $job->tag(trim($tag));
                    }
                }
            }

            return response()->json([
                'message' => '✅ Uspješno importirano!',
                'redirect' => url('/jobs'),
            ]);
        }
}