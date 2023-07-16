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

        Article::factory(8)->state(new Sequence(
            [
                'title' => '„Vreau să joc” - autobiografia lui Ionuț Rada', 
                'description' => 'Povestea a început în urmă cu 10 ani, când, cu mult entuziasm și emoție, m-am apucat să îmi notez una și alta.
                În 2020, am dat startul acestui proiect care acum va ajunge la voi. Îmi doresc din toată inima să inspir și să
                transmit celor din jur pasiunea mea pentru sport.', 
                'link' => 'https://www.the-rada-way.ro', 
                'imagelink' => 'images/articles/poza-coperta-carte-ionut-rada.jpeg', 
                'created_at' => '2023-07-10',
                'category_id' => 1,//learn
                'position' => 1
            ],
            [
                'title' => 'Ionut Rada, creatorul platformei de antrenamente fitness online The Rada Way', 
                'description' => '„The Rada Way” este platforma mea de antrenamente fitness online, in care am investit toata experienta si energia mea, cu un singur scop: sa te ajut sa integrezi sportul in viata ta si sa te bucuri de beneficiile unui stil de viata activ.', 
                'link' => 'https://www.the-rada-way.ro', 
                'imagelink' => 'images/articles/antrenamente-fitness-online-ionut-rada.jpeg', 
                'created_at' => '2023-07-03',
                'category_id' => 1,//learn
                'position' => 2
            ],
            [
                'title' => 'Fișa postului / fundaș central', 
                'description' => 'Aici este pe felia mea să zic așa😃
                Am încerc în acest clip să vin cu câteva detalii importante, în mare, pentru ca sunt multe de discutat și descoperit pe această poziție.
                Abonați-vă și hai la fotbal!
                Pentru colaboratori întrați pe www.theradaway.ro si la sectiunea contact@theradaway.ro scrieți messjul VREAU SĂ JOC
                #TheRadaWay #Educație', 
                'link' => 'https://youtu.be/O0PR3H8W14k', 
                'imagelink' => 'https://i3.ytimg.com/vi/O0PR3H8W14k/hqdefault.jpg', 
                'created_at' => '2023-01-11',
                'category_id' => 3,//fotbal
                'position' => 3
            ],
            [
                'title' => 'Cum o arată o gustare TheRadaWay după un antrenament?', 
                'description' => 'Cum o arată o gustare TheRadaWay după un antrenament TheRadaWay😀
                V-am făcut poftă?😜
                Cum arăta farfuria voastră?😁
                Bio Culture #alimentațiesanatoasă 
                www.theradaway.ro ⬅️
                #TheRadaWay #VreauSaJoc #pasiune #sport #emoție', 
                'link' => 'https://youtu.be/kE91WV3ugBI', 
                'imagelink' => 'https://i3.ytimg.com/vi/kE91WV3ugBI/hqdefault.jpg', 
                'created_at' => '2023-06-28',
                'category_id' => 2,//cardio
                'position' => 4
            ],
            
            [
                'title' => 'Alergare pe căldură / de ce să țineți cont?', 
                'description' => 'Calendarul TheRadaWay începe tot timpul duminica, trupă!💪
                Alergare,  sport  pe temperaturi ridicate, câteva lucruri de care să țineți cont🤔
                Sportul continuă și pe frig și pe căldură👏
                www.theradaway.ro ⬅️
                #TheRadaWay #VreauSaJoc #pasiune #sport #emoție', 
                'link' => 'https://youtu.be/ZokNgpvpspM', 
                'imagelink' => 'https://i3.ytimg.com/vi/ZokNgpvpspM/hqdefault.jpg', 
                'created_at' => '2023-07-09',
                'category_id' => 2,//cardio
                'position' => 5
            ],
            
            [
                'title' => 'Repriza de mișcare / antrenament TheRadaWay', 
                'description' => 'Marțea activă, trupă!💪
                Antrenamentele TheRadaWay sunt o combinație din școala alergării, pilates, yoga, cardio, exerciții care să te ajute să ai o viață sănătoasă!💪
                Cel mai important este că antrenamentele TheRadaWay le faci cu zâmbetul pe buze😀
                www.theradaway.ro ⬅️
                #TheRadaWay #VreauSaJoc #pasiune #sport #emoție', 
                'link' => 'https://youtu.be/dQsBY2-pxnw', 
                'imagelink' => 'https://i3.ytimg.com/vi/dQsBY2-pxnw/hqdefault.jpg', 
                'created_at' => '2023-07-11',
                'category_id' => 2,//cardio
                'position' => 6
            ],

            [
                'title' => ' Bună dimineața trupă ☀️', 
                'description' => ' Bună dimineața trupă ☀️', 
                'link' => 'https://youtu.be/_CqQEIhwp7U', 
                'imagelink' => 'https://i3.ytimg.com/vi/_CqQEIhwp7U/hqdefault.jpg', 
                'created_at' => '2023-07-01',
                'category_id' => 2,//cardio
                'position' => 7
            ],

            [
                'title' => 'Prieteni de ocazie / Cum ați renunțat la ei?', 
                'description' => 'Calendarul TheRadaWay începe tot timpul duminica,  trupă!💪
                Cum vă descurcați cu prietenii de ocazie?😁
                Sunt curios cum ați întrerupt aceste relații😅
                Rămâneți activi chiar și după o astfel de relație 😀
                www.theradaway.ro ⬅️
                #TheRadaWay #VreauSaJoc #pasiune #sport #emoție', 
                'link' => 'https://youtu.be/W2UP5JDDM_o', 
                'imagelink' => 'https://i3.ytimg.com/vi/W2UP5JDDM_o/hqdefault.jpg', 
                'created_at' => '2023-07-16',
                'category_id' => 2,//cardio
                'position' => 8
            ],
            
        ))->create();
    }
}
