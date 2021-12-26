<?php

use App\Models\{
    User,
    Preferences
};
use Illuminate\Support\Facades\Route;

Route::get('/one-to-one', function () {
    //Rota para testes da relação one to one
    //Criação, Modificação e exclusão

    //$user = User::first();
    $user = User::with('preferences')->first();//Sempre usar o with, que diminui as consultas ao banco

        $data =[
            'background_color' => '#000',
        ];


    if ($user->preferences) {//Se já existir as preferencias em um usuário, atualiza
        $user->preferences->update($data);
    } else {//Caso não haja, cria uma nova
        $user->preferences()->create($data);
        //$preferences = new Preferences($data);
        //$user->preferences()->save($preferences);
    }
    
    //Para deletar
    //$user->preferences->delete();

    dd($user->preferences);
});

Route::get('/', function () {
    return view('welcome');
});
