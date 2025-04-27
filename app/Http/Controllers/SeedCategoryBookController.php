<?php

namespace App\Http\Controllers;

use App\Models\CategoryBook;
use App\Models\User;
use App\Models\Comment;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as faker;

class SeedCategoryBookController extends Controller
{

    public function index()
    {
        CategoryBook::truncate();
        $cat0  = CategoryBook::create(['name' => 'دسته بندی']);
        $cat1= tap(CategoryBook::create(['name' => 'دوره آموزش ابتدایی']), fn($node) => $node->appendToNode($cat0)->save());
        $cat2 = tap(CategoryBook::create(['name' => 'دوره اول آموزش متوسطه']), fn($node) => $node->appendToNode($cat0)->save());
        $cat3 = tap(CategoryBook::create(['name' => 'دوره دوم آموزش متوسطه']), fn($node) => $node->appendToNode($cat0)->save());
        $cat4 = tap(CategoryBook::create(['name' => 'راهنمای تدریس']), fn($node) => $node->appendToNode($cat0)->save());
        $cat5 = tap(CategoryBook::create(['name' => 'دوره آموزش راهنمایی (قدیم)']), fn($node) => $node->appendToNode($cat0)->save());
        $cat6 = tap(CategoryBook::create(['name' => 'کتاب های درسی استثنایی']), fn($node) => $node->appendToNode($cat0)->save());

        $cat7 = tap(CategoryBook::create(['name' => 'پایه اول']), fn($node) => $node->appendToNode($cat1)->save());
        $cat8 = tap(CategoryBook::create(['name' => 'پایه دوم']), fn($node) => $node->appendToNode($cat1)->save());
        $cat9 = tap(CategoryBook::create(['name' => 'پایه سوم']), fn($node) => $node->appendToNode($cat1)->save());
        $cat10 = tap(CategoryBook::create(['name' => 'پایه چهارم']), fn($node) => $node->appendToNode($cat1)->save());
        $cat11 = tap(CategoryBook::create(['name' => 'پایه پنجم']), fn($node) => $node->appendToNode($cat1)->save());
        $cat12 = tap(CategoryBook::create(['name' => 'پایه ششم']), fn($node) => $node->appendToNode($cat1)->save());

        $cat13 = tap(CategoryBook::create(['name' => 'پایه هفتم']), fn($node) => $node->appendToNode($cat2)->save());
        $cat14 = tap(CategoryBook::create(['name' => 'پایه هشتم']), fn($node) => $node->appendToNode($cat2)->save());
        $cat15 = tap(CategoryBook::create(['name' => 'پایه نهم']), fn($node) => $node->appendToNode($cat2)->save());


        $cat16 = tap(CategoryBook::create(['name' => 'متوسطه نظری']), fn($node) => $node->appendToNode($cat3)->save());
        $cat17 = tap(CategoryBook::create(['name' => 'فنی حرفه ای']), fn($node) => $node->appendToNode($cat3)->save());
        $cat18 = tap(CategoryBook::create(['name' => 'کار دانش']), fn($node) => $node->appendToNode($cat3)->save());
        $cat19 = tap(CategoryBook::create(['name' => 'جغرافيای استان ها']), fn($node) => $node->appendToNode($cat3)->save());
        $cat20 = tap(CategoryBook::create(['name' => 'سال اول متوسطه قدیم']), fn($node) => $node->appendToNode($cat3)->save());
        $cat21 = tap(CategoryBook::create(['name' => 'دروس عمومی سال دوم وسوم متوسطه قدیم']), fn($node) => $node->appendToNode($cat3)->save());


        $cat22 = tap(CategoryBook::create(['name' => 'ریاضی فیزیک']), fn($node) => $node->appendToNode($cat16)->save());
        $cat23 = tap(CategoryBook::create(['name' => 'علوم تجربی']), fn($node) => $node->appendToNode($cat16)->save());
        $cat24 = tap(CategoryBook::create(['name' => 'علوم انسانی']), fn($node) => $node->appendToNode($cat16)->save());
        $cat25 = tap(CategoryBook::create(['name' => 'علوم و معارف اسلامی']), fn($node) => $node->appendToNode($cat16)->save());
        $cat26 = tap(CategoryBook::create(['name' => 'هنر']), fn($node) => $node->appendToNode($cat16)->save());


        $cat27 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat22)->save());
        $cat28 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat22)->save());
        $cat29 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat22)->save());
        $cat30 = tap(CategoryBook::create(['name' => 'پیش دانشگاهی']), fn($node) => $node->appendToNode($cat22)->save());

        $cat31 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat23)->save());
        $cat32 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat23)->save());
        $cat33 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat23)->save());
        $cat34 = tap(CategoryBook::create(['name' => 'پیش دانشگاهی']), fn($node) => $node->appendToNode($cat23)->save());

        $cat35 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat24)->save());
        $cat36 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat24)->save());
        $cat37 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat24)->save());
        $cat38 = tap(CategoryBook::create(['name' => 'پیش دانشگاهی']), fn($node) => $node->appendToNode($cat24)->save());

        $cat39 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat25)->save());
        $cat40 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat25)->save());
        $cat41 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat25)->save());
        $cat42 = tap(CategoryBook::create(['name' => 'پیش دانشگاهی']), fn($node) => $node->appendToNode($cat25)->save());

        $cat43 = tap(CategoryBook::create(['name' => 'پیش دانشگاهی']), fn($node) => $node->appendToNode($cat26)->save());



        $cat44 = tap(CategoryBook::create(['name' => 'دروس مشترك فنی و حرفه ای']), fn($node) => $node->appendToNode($cat17)->save());
        $cat45 = tap(CategoryBook::create(['name' => 'صنعت']), fn($node) => $node->appendToNode($cat17)->save());
        $cat46 = tap(CategoryBook::create(['name' => 'خدمات']), fn($node) => $node->appendToNode($cat17)->save());
        $cat47 = tap(CategoryBook::create(['name' => 'کشاورزی']), fn($node) => $node->appendToNode($cat17)->save());
        $cat48 = tap(CategoryBook::create(['name' => 'هنر']), fn($node) => $node->appendToNode($cat17)->save());

        $cat49 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat44)->save());
        $cat50 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat44)->save());
        $cat51 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat44)->save());

        $cat52 = tap(CategoryBook::create(['name' => 'گروه برق و رایانه']), fn($node) => $node->appendToNode($cat45)->save());
        $cat53 = tap(CategoryBook::create(['name' => 'گروه تعمیر و نگهداری ماشین آلات']), fn($node) => $node->appendToNode($cat45)->save());
        $cat54 = tap(CategoryBook::create(['name' => 'گروه معماری و ساختمان']), fn($node) => $node->appendToNode($cat45)->save());
        $cat55 = tap(CategoryBook::create(['name' => 'گروه مواد و فرآوری']), fn($node) => $node->appendToNode($cat45)->save());
        $cat56 = tap(CategoryBook::create(['name' => 'گروه مکانیک']), fn($node) => $node->appendToNode($cat45)->save());

        $cat57 = tap(CategoryBook::create(['name' => 'الکتروتکنیک']), fn($node) => $node->appendToNode($cat52)->save());
        $cat58 = tap(CategoryBook::create(['name' => 'الکترونیک']), fn($node) => $node->appendToNode($cat52)->save());
        $cat59 = tap(CategoryBook::create(['name' => 'شبکه و نرم افزار رایانه']), fn($node) => $node->appendToNode($cat52)->save());
        $cat60 = tap(CategoryBook::create(['name' => 'الکترونیک و مخابرات دریایی']), fn($node) => $node->appendToNode($cat52)->save());

        $cat61 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat57)->save());
        $cat62 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat57)->save());
        $cat63 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat57)->save());

        $cat64 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat58)->save());
        $cat65 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat58)->save());
        $cat66 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat58)->save());

        $cat67 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat59)->save());
        $cat68 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat59)->save());
        $cat69 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat59)->save());

        $cat70 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat60)->save());
        $cat71 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat60)->save());
        $cat72 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat60)->save());

        $cat73 = tap(CategoryBook::create(['name' => 'مکانیک موتورهای دریایی']), fn($node) => $node->appendToNode($cat53)->save());

        $cat74 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat73)->save());
        $cat75 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat73)->save());
        $cat76 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat73)->save());

        $cat77 = tap(CategoryBook::create(['name' => 'ساختمان']), fn($node) => $node->appendToNode($cat54)->save());
        $cat78 = tap(CategoryBook::create(['name' => 'نقشه برداری']), fn($node) => $node->appendToNode($cat54)->save());

        $cat79 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat77)->save());
        $cat80 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat77)->save());
        $cat81 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat77)->save());

        $cat82 = tap(CategoryBook::create(['name' => 'سال دوم']), fn($node) => $node->appendToNode($cat78)->save());
        $cat83 = tap(CategoryBook::create(['name' => 'سال سوم']), fn($node) => $node->appendToNode($cat78)->save());

        $cat84 = tap(CategoryBook::create(['name' => 'سرامیک']), fn($node) => $node->appendToNode($cat55)->save());
        $cat85 = tap(CategoryBook::create(['name' => 'صنایع شیمیایی']), fn($node) => $node->appendToNode($cat55)->save());
        $cat86 = tap(CategoryBook::create(['name' => 'صنایع نساجی']), fn($node) => $node->appendToNode($cat55)->save());
        $cat87 = tap(CategoryBook::create(['name' => 'متالورژی']), fn($node) => $node->appendToNode($cat55)->save());
        $cat88 = tap(CategoryBook::create(['name' => 'معدن']), fn($node) => $node->appendToNode($cat55)->save());


        $cat89 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat84)->save());
        $cat90 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat84)->save());
        $cat91 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat84)->save());

        $cat92 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat85)->save());
        $cat93 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat85)->save());
        $cat94 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat85)->save());

        $cat95 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat86)->save());
        $cat96 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat86)->save());
        $cat97 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat86)->save());

        $cat98 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat87)->save());
        $cat99 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat87)->save());
        $cat100 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat87)->save());

        $cat101 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat88)->save());
        $cat102 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat88)->save());
        $cat103 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat88)->save());

        $cat103 = tap(CategoryBook::create(['name' => 'مکاترونیک']), fn($node) => $node->appendToNode($cat56)->save());
        $cat104 = tap(CategoryBook::create(['name' => 'صنایع فلزی']), fn($node) => $node->appendToNode($cat56)->save());
        $cat105 = tap(CategoryBook::create(['name' => 'تأسیسات مکانیکی']), fn($node) => $node->appendToNode($cat56)->save());
        $cat106 = tap(CategoryBook::create(['name' => 'مکانیک موتورهای دریایی']), fn($node) => $node->appendToNode($cat56)->save());
        $cat107 = tap(CategoryBook::create(['name' => 'مکانیک خودرو']), fn($node) => $node->appendToNode($cat56)->save());
        $cat108 = tap(CategoryBook::create(['name' => 'ماشین ابزار']), fn($node) => $node->appendToNode($cat56)->save());
        $cat109 = tap(CategoryBook::create(['name' => 'صنایع چوب و مبلمان']), fn($node) => $node->appendToNode($cat56)->save());
        $cat110 = tap(CategoryBook::create(['name' => 'چاپ']), fn($node) => $node->appendToNode($cat56)->save());
        $cat111 = tap(CategoryBook::create(['name' => 'تاسیسات']), fn($node) => $node->appendToNode($cat56)->save());
        $cat112 = tap(CategoryBook::create(['name' => 'صنایع چوب و کاغذ']), fn($node) => $node->appendToNode($cat56)->save());
        $cat113 = tap(CategoryBook::create(['name' => 'ساخت و تولید']), fn($node) => $node->appendToNode($cat56)->save());
        $cat114 = tap(CategoryBook::create(['name' => 'نقشه کشی عمومی']), fn($node) => $node->appendToNode($cat56)->save());

        $cat115 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat103)->save());
        $cat116 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat103)->save());
        $cat117 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat103)->save());

        $cat118 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat104)->save());
        $cat119 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat104)->save());
        $cat120 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat104)->save());

        $cat121 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat105)->save());
        $cat122 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat105)->save());
        $cat123 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat105)->save());

        $cat124 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat106)->save());
        $cat125 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat106)->save());
        $cat126 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat106)->save());

        $cat127 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat107)->save());
        $cat128 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat107)->save());
        $cat129 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat107)->save());

        $cat130 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat108)->save());
        $cat131 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat108)->save());
        $cat132 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat108)->save());

        $cat133 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat109)->save());
        $cat134 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat109)->save());
        $cat135 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat109)->save());

        $cat136 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($cat110)->save());
        $cat137 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($cat110)->save());
        $cat138 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($cat110)->save());

        $cat139 = tap(CategoryBook::create(['name' => 'سال دوم']), fn($node) => $node->appendToNode($cat111)->save());
        $cat140 = tap(CategoryBook::create(['name' => 'سال سوم']), fn($node) => $node->appendToNode($cat111)->save());

        $cat141 = tap(CategoryBook::create(['name' => 'سال دوم']), fn($node) => $node->appendToNode($cat112)->save());
        $cat142 = tap(CategoryBook::create(['name' => 'سال سوم']), fn($node) => $node->appendToNode($cat112)->save());

        $cat143 = tap(CategoryBook::create(['name' => 'سال دوم']), fn($node) => $node->appendToNode($cat113)->save());
        $cat144 = tap(CategoryBook::create(['name' => 'سال سوم']), fn($node) => $node->appendToNode($cat113)->save());

        $cat145 = tap(CategoryBook::create(['name' => 'سال دوم']), fn($node) => $node->appendToNode($cat114)->save());
        $cat146 = tap(CategoryBook::create(['name' => 'سال سوم']), fn($node) => $node->appendToNode($cat114)->save());


        $cat1 = tap(CategoryBook::create(['name' => '']), fn($node) => $node->appendToNode($cat1)->save());

    }

    public function addThreecategory(CategoryBook $category)
    {
        // dd($category);
        // for($i=746; $i<=786; $i++)
        // {
        //     $category = Category::find($i);
            $cat136 = tap(CategoryBook::create(['name' => 'پایه دهم']), fn($node) => $node->appendToNode($category)->save());
            $cat137 = tap(CategoryBook::create(['name' => 'پایه یازدهم']), fn($node) => $node->appendToNode($category)->save());
            $cat138 = tap(CategoryBook::create(['name' => 'پایه دوازدهم']), fn($node) => $node->appendToNode($category)->save());
        // }

    }
}
