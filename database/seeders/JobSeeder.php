<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use App\Models\Job;
use App\Models\Tag;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void 
        { 
            $tags = Tag::factory(2)->create(); 

            Job::factory(20)->hasAttached($tags)->create(new Sequence([ 
                'featured' => false, 
                'schedule' => 'Full Time', 
            ],[
                'featured' => true, 
                'schedule' => 'Part Time', 
            ])); 
        }
}
