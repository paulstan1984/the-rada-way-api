<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        DB::table('articles')->truncate();
        DB::table('categories')->truncate();

        Schema::enableForeignKeyConstraints();

        Category::factory(3)->state(new Sequence(
            [
                'name' => 'Educație',
                'imagelink' => ''
            ],
            [
                'name' => 'Exerciții cardio',
                'imagelink' => ''
            ],
            [
                'name' => 'Fodbal',
                'imagelink' => ''
            ]
        ))
            ->create();

        Article::factory(1)->state(new Sequence(
            [
                'title' => '', 
                'description' => '', 
                'link' => '', 
                'imagelink' => '', 
                'category_id' => 1
            ]
        ))->create();
    }
}
