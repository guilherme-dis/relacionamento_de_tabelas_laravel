<?php

use App\Models\{
    User,
    Course,
    Permission,
    Image
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
    $course = Course::with('modules.lessons')->first();

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
    foreach ($course->modules as $module) {
        echo "Módulo {$module->name}<br>";
        foreach ($module->lessons as $lesson) {
            echo "Aula {$lesson->name}<br>";
        }
    }

    //Criando o módulo
    //$course->modules()->create($modulo);

    $module = $course->modules; //caso vazio, retorna vazio e não nulo

    //$module->find(2)->lessons()->create($curso);
    dd($module->find(2)->lessons);
});

Route::get('/many-to-many', function () {

    //Criando uma permissão nova
    //dd(Permission::create(['name'=>'menu_03']));

    //Recupera um usuário e já traz as permissions
    //$user = User::with('permissions')->find(1);

    //Jogando para dentro das permissions a permission de id 1
    //$permission = Permission::find(1);

    //Salvando uma permissão no usuário
    //$user->permissions()->save($permission);

    //Salvando várias permissões ao mesmo tempo
    /*
    $user->permissions()->saveMany([
        Permission::find(1),
        Permission::find(2),
        //Permission::find(3),
    ]);*/

    //o sync deleta o que tinha(exeto os repetidos) e coloca os novos
    //$user->permissions()->sync([2]);


    //adiciona um novo independente dos que já existem
    //$user->permissions()->attach([1]);

    //Desincroniza a permissão de id 1 desse usuário
    //$user->permissions()->detach([1]);


    //$user->refresh();

    //dd($user->permissions);
});

Route::get('/many-to-many-pivot', function () {
    //NÃO ESQUECER DE COLOCAR NO MODEL USER, A FUNÇÃO: ->withPivot(['active']);
    //Pegando um usuário    
    //$user = User::with('permissions')->find(1);

    //Adcionando permissões nesse user, mas alterando uns dados da tabela pivot
    // $user->permissions()->attach([
    //     1=>['active'=>false],//Adicionando uma permissão pelo id, e alterando se está ativo.
    //     3=>['active'=>false],
    // ]);

    //$user->refresh();

    //Printando todas as permissões de um usuário, e printando um atributo da tabela pivot
    // foreach ($user->permissions as $permission) {
    //     echo "{$permission->name} - {$permission->pivot->active}<br>";
    // }
});

Route::get('/one-to-one-polymorphic', function () {

    //Pegar o primeiro usuário
    //$user = User::first();

    //$data=['path'=>'path caminho'];

    //Caso o usuário não tenha image, cria, caso contrário, faz o update

    /*
    if($user->image){
        $user->image->update($data);
    }else{
        //$user->image()->create($data);
        $user->image()->save(
        new Image(['path'=>'paht/nome-image'])
        );
    }*/

    //Para deletar
    //$user->image->delete();



    //dd($user->image->path);
});


Route::get('/', function () {
    return view('welcome');
});
