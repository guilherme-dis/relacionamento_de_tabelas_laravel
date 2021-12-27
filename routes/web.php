<?php

use App\Models\{
    User,
    Course
};
use Illuminate\Support\Facades\Route;

Route::get('/one-to-one', function () {
    //Rota para testes da relação one to one
    //Criação, Modificação e exclusão

    //$user = User::first();
    $user = User::with('preferences')->first(); //Sempre usar o with, que diminui as consultas ao banco

    $data = [
        'background_color' => '#000',
    ];

    if ($user->preferences) { //Se já existir as preferencias em um usuário, atualiza
        $user->preferences->update($data);
    } else { //Caso não haja, cria uma nova
        $user->preferences()->create($data);
        //$preferences = new Preferences($data);
        //$user->preferences()->save($preferences);
    }

    //Para deletar
    //$user->preferences->delete();

    dd($user->preferences);
});








Route::get('/one-to-many', function () {


    //Criei um primeiro curso no banco de dados.
    //$course = Course::create(['name' => 'Curso de Laravel','']);

    //Estou pegando um curso, no caso o primeiro
    $course=Course::with('modules.lessons')->first();
    
    //Simulando um request
    /*
    $modulo=['name'=>'laravel x1'];
    $curso=[
        'name'=>'Eloquente',
        'video'=>'v1',
    ];*/

    //Loop para mostrar todos os módulos e aulas
    echo $course->name;
    echo '<br>';
    foreach($course->modules as $module){
        echo "Módulo {$module->name}<br>";
        foreach($module->lessons as $lesson){
            echo "Aula {$lesson->name}<br>";
        }
    }

    //Criando o módulo
    //$course->modules()->create($modulo);
    
    $module=$course->modules;//caso vazio, retorna vazio e não nulo
    
    //$module->find(2)->lessons()->create($curso);
    dd($module->find(2)->lessons);

});





Route::get('/', function () {
    return view('welcome');
});
