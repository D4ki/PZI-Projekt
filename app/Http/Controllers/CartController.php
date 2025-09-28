<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    public function add(Job $job)
    {
        $cart = session()->get('cart', []);

        // Ako već postoji → ignoriraj
        if (!isset($cart[$job->id])) {
            $cart[$job->id] = [
                'title' => $job->title,
                'salary' => $job->salary,
                'employer' => $job->employer->name ?? 'Unknown',
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Job added to cart!');
    }

    public function remove(Job $job)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$job->id])) {
            unset($cart[$job->id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Job removed!');
    }
}
