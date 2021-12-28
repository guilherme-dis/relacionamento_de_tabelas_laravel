HasOne é para a classe que vai ter o id
belongsTo é a classe que importa o id


one to one é nos arquivos 
preferences e user


one to many é nos course, lesson e module

many to many é as permissions


many to many pivot, recupera os dados que liga as duas tabelas

Quando vamos fazer o polimorfismo, temos uma tabela de imagem, e classes que tem imagens, assim, lincamos tudo aqui.
As configurações estão na migrate image, e o model


<hr>
<h1>ONE - TO - MANY</h1> <br>

~~~
sail artisa make:model Comment -m
~~~
\App\Models\Comment
~~~
public function commentable(){
        return $this->morphTo();
    }
~~~
a migration fica:
~~~
    $table->string('subject');              //Atributos do comentário     
    $table->text('content');                //Atributos do comentário
    $table->morphs('commentable');          
~~~
Agora é só colocar essa função abaixo em qualquer classe que desejar, no caso relacionar um comentário com o que vc quiser.

~~~
public function comments(){
    return $this->morphMany(Comment::class,'commentable');
    }
~~~

Um exemplo de uso seria:

~~~
    //Pegando um curso
    // $course = Course::first();

    //Fazendo um comentário para esse curso
    // $course->comments()->create([
    //     'subject'=>'Novo Comentário',
    //     'content'=>'Apenas um comentário'
    // ]);

    //Fazendo a mesma coisa para lessons
    // $lesson=Lesson::find(1);
    // $lesson->comments()->create([
    //     'subject'=>'Novo Comentário',
    //     'content'=>'Apenas um comentário'
    // ]);


    //pegando um comentário e imprimindo
    //$comment=Comment::find(1);
    //dd($comment->commentable);
~~~


<hr>
<h1> MANY - TO - MANY</h1>