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
                'imagelink' => 'images/categories/learn.jpg',
                'position' => 1
            ],
            [
                'name' => 'Exerciții cardio',
                'imagelink' => 'images/categories/cardio.jpg',
                'position' => 2
            ],
            [
                'name' => 'Fotbal',
                'imagelink' => 'images/categories/fotbal.jpg',
                'position' => 3
            ]
        ))
            ->create();

        Article::factory(4)->state(new Sequence(
            //learn
            [
                'title' => '„Vreau să joc” - autobiografia lui Ionuț Rada', 
                'description' => 'Povestea a început în urmă cu 10 ani, când, cu mult entuziasm și emoție, m-am apucat să îmi notez una și alta.
                În 2020, am dat startul acestui proiect care acum va ajunge la voi. Îmi doresc din toată inima să inspir și să
                transmit celor din jur pasiunea mea pentru sport.', 
                'link' => 'https://www.the-rada-way.ro', 
                'imagelink' => 'images/articles/poza-coperta-carte-ionut-rada.jpeg', 
                'created_at' => '2023-07-10',
                'category_id' => 1,
                'position' => 1
            ],
            [
                'title' => 'Ionut Rada, creatorul platformei de antrenamente fitness online The Rada Way', 
                'description' => '„The Rada Way” este platforma mea de antrenamente fitness online, in care am investit toata experienta si energia mea, cu un singur scop: sa te ajut sa integrezi sportul in viata ta si sa te bucuri de beneficiile unui stil de viata activ.', 
                'link' => 'https://www.the-rada-way.ro', 
                'imagelink' => 'images/articles/antrenamente-fitness-online-ionut-rada.jpeg', 
                'created_at' => '2023-07-03',
                'category_id' => 1,
                'position' => 2
            ],
            //cardio
            [
                'title' => 'Repriza de mișcare / antrenament TheRadaWay', 
                'description' => 'Marțea activă, trupă!💪
                Antrenamentele TheRadaWay sunt o combinație din școala alergării, pilates, yoga, cardio, exerciții care să te ajute să ai o viață sănătoasă!💪
                Cel mai important este că antrenamentele TheRadaWay le faci cu zâmbetul pe buze😀
                www.theradaway.ro ⬅️
                #TheRadaWay #VreauSaJoc #pasiune #sport #emoție', 
                'link' => 'https://youtu.be/dQsBY2-pxnw', 
                'imagelink' => 'http://i3.ytimg.com/vi/dQsBY2-pxnw/hqdefault.jpg', 
                'created_at' => '2023-07-11',
                'category_id' => 2,
                'position' => 1
            ],
            //fotbal
            [
                'title' => 'Fișa postului / fundaș central', 
                'description' => 'Aici este pe felia mea să zic așa😃
                Am încerc în acest clip să vin cu câteva detalii importante, în mare, pentru ca sunt multe de discutat și descoperit pe această poziție.
                Abonați-vă și hai la fotbal!
                Pentru colaboratori întrați pe www.theradaway.ro si la sectiunea contact@theradaway.ro scrieți messjul VREAU SĂ JOC
                #TheRadaWay #Educație', 
                'link' => 'https://youtu.be/O0PR3H8W14k', 
                'imagelink' => 'http://i3.ytimg.com/vi/dQsBY2-pxnw/hqdefault.jpg', 
                'created_at' => '2023-01-11',
                'category_id' => 3,
                'position' => 1
            ],
            
        ))->create();
    }
}
