# Relacionamento Entre tabelas no Laravel

_esse artigo é para mostrar como faz as relações, e não explicar o conceito de cada um!_

# ONE TO ONE

Temos duas tabelas:

-   User
-   Preferences

User (1,1) <=> (0,1) Preferences

Primeiramente temos que analisar qual tabela vai importar o id da outra para ser a chave estrangeira.

Preferences vai importar o id de user, sendo assim, cada model ganhas as funções:

\App\Models\Preference

```PHP
    //Model preference
    public function user()
    {
        return $this->belongsTo(User::class);//Importa o id do usuário
    }
```

\App\Models\User

```PHP
    //Model User
    public function preferences()
    {//Em relação com user, essa é a tabela mais fraca porque ela importa o id
        return $this->hasOne(Preferences::class);//Cede o seu id para ser uma fk em preferences
    }
```

### Migrations:

User não tem modificação.

Preference precisa ser declarado a fk:

```PHP
    Schema::create('preferences', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users');//O último argumento é o nome da tabela, e o primeiro é o nome da chave
        $table->boolean('notify_emails')->default(true);
        $table->boolean('notify')->default(true);
        $table->string('background_color');
        $table->timestamps();
    });
```

---

# ONE TO MANY

Temos três tabelas.

-   Course
-   Module
-   Lesson

Curso (1,1) <=>(1,n) Module.

Module (1,1) <=> (1,n) Lesson.

Modulo vai importar o id do curso, e lesson vai importar o id do módulo.

\.

\App\Models\Course

```PHP
    //Course
    public function modules(){
        return $this->hasMany(Module::class);//Curse tem vários módulos.
    }
```

\App\Models\Module.

```PHP
    //Module.
    //Em relação a tabela course, essa é a tabela fraca pois ela importa o id
    public function course()
    {   //Importando o id do curso.
        return $this->belongsTo(Course::class);
    }

    //Em relação com a tabela lessons, module é forte, por isso está
    public function lessons()
    {   //Tem várias lessons.
        return $this->hasMany(Lesson::class);
    }
```

\App\Modules\Lesson

```PHP
    //Lesson
    public function module()
    {
        //importando o id do módulo.
        return $this->belongsTo(Module::class);
    }

```

### Migrations:

Course não tem modificações na migrate.

Module:

```PHP
    Schema::create('modules', function (Blueprint $table) {
        $table->id();
        //Somente salvando o id do curso como uma fk
        $table->foreignId('course_id')->constrained('courses');
        $table->string('name');
        $table->timestamps();
    });
```

Lesson:

```PHP
    Schema::create('lessons', function (Blueprint $table) {
        $table->id();
        $table->foreignId('module_id')->constrained('modules');//Somente salvando o id do modulo como uma fk
        $table->string('name');
        $table->string('video');
        $table->timestamps();
    });
```

# MANY TO MANY

Temos Três tabelas.

-   User
-   Permission
-   permission_user //sendo a tabela intermediária (pivot)

User (1,n) <=>(0,n) Permission.

\App\Models\User

```PHP
    //User
    public function permissions()
    {
        return $this->belongsToMany(Permission::class)
            ->withPivot(['active']); //através do relacionamento, retornar valores da tabela pivot, no caso o atributo active.
    }
```

\App\Models\Permission

```PHP
    //Permission
    public function users(){
        return $this->belongsToMany(User::class);
    }
```

### Migrations:

A tabela user não precisa de modificação na migration.

A tabela permission não precisa de nada fora do comum.

A tabela intermediária(pivot) é criada dentro do arquivo da migrate permission. _Não é uma má pratica de programação isso. Pelo contrario, fica mais organizado._

permission_user:

```PHP
    //permission_user
    public function up()
    {//importante ser criada dps de usuários
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('permission_user', function (Blueprint $table) {
            $table->id();
            //Pega o id da permission.
            $table->foreignId('permission_id')->constrained('permissions');
            //Pega o id do user.
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('active')->default(true);

        });
    }
    public function down()
    {   Schema::dropIfExists('permissions_user');//tem que deletar esse primeiro pq se não não vai aceitar deletar permission pq tem uma tabela ligada a ela
        Schema::dropIfExists('permissions');
    }
```

Podemos recuperar atributos da classe Intermediária(pivot) dessa forma:

```PHP
    //NÃO ESQUECER DE COLOCAR NO MODEL USER, A FUNÇÃO: ->withPivot(['active']);
    //Pegando um usuário
    $user = User::with('permissions')->find(1);

    //Adcionando permissões nesse user, mas alterando uns dados da tabela pivot
     $user->permissions()->attach([
         1=>['active'=>false],//Adicionando uma permissão pelo id, e alterando se está ativo ou não.
         3=>['active'=>false],
     ]);

    $user->refresh();

    //Printando todas as permissões de um usuário, e printando um atributo da tabela pivot
     foreach ($user->permissions as $permission) {
         echo "{$permission->name} - {$permission->pivot->active}<br>";
     }
```

# ONE TO ONE - POLYMORPHIC

Nessa relação vou explicar como funciona.

Em um sistema, talvez podemos ter a seguinte realção: várias tabelas possa se relacionar com uma tabela específica.

Para que não haja tabelas como user-image, lesson-image. Criamos uma tabla genérica Image, e relacionamos todas as classes que necessitam de imagem com a tabela image.

**Caso não tenha entendido o conseito, pesquise por fora, eu demorei para entender também**

\App\Models\Image

```PHP
    //Image
    public function imageable(){
        return $this->morphTo();
    }
```

**Agora vc precisa relacionar image com todas as classes que vão precisar dessa relação**, vamos relacionar com User e Lesson para exemplificar.

Em cada Model, insira esse código que vai funcionar:

```PHP
    public function image()
    {
        return $this->morphOne(Image::class,'imageable');//imageable é o nome da coluna do image que faz o relacionamento.
    }
```

### Migrations:

Images_table

```PHP
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            //Esse atributo aqui, é 2 em um,(Vamos pegar user como exemplo), ele cria a coluna id do usuário e o tipo.
            //Por exemplo, ele olha o tipo, e sabe em que tabela procurar o id.
            $table->morphs('imageable');
            $table->timestamps();
        });
```

# ONE - TO - MANY - POLYMORPHIC

É interessante utilizar o relacionamento Polimorfico one to many quando temos um cenário parecido com esse: Temos a classe comentário e queremos relacionar essa classe com várias outras,(User,Lesson,Course), mas não queremos fazer uma classe para cada relacionamento. Assim criamos um relacionamento genérico.

\App\Models\Comment

```PHP
    public function commentable(){
        return $this->morphTo();
    }
```

### Migrations:

```PHP
    Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->string('subject');//Atributo do comentário.
            $table->text('content');//Atributo do comentário.
            $table->morphs('commentable');
            $table->timestamps();
        });
```

Agora é só colocar essa função abaixo em qualquer classe que desejar, no caso relacionar um comentário com o que vc quiser.

```PHP
public function comments(){
    return $this->morphMany(Comment::class,'commentable');
    }
```

Um exemplo de uso seria:

```PHP
   
    
    //Pegando um curso
    //Fazendo um comentário para esse curso
    $course = Course::first();
    $course->comments()->create([
        'subject'=>'Novo Comentário',
        'content'=>'Apenas um comentário'
    ]);

    //Fazendo a mesma coisa para lessons
    $lesson=Lesson::find(1);
    $lesson->comments()->create([
        'subject'=>'Novo Comentário',
        'content'=>'Apenas um comentário'
    ]);


    //pegando um comentário e imprimindo
    $comment=Comment::find(1);
    dd($comment->commentable);
```

# MANY TO MANY - POLYMORPHIC
Vamos relacionar Tag's com as classes que queremos, sendo assim, no model, precisamos fazer uma função com todos


\App\Models\Tag
~~~PHP
    public function users(){
        return $this->morphedByMany(User::class,'taggable');
    }

    public function courses(){
        return $this->morphedByMany(Course::class,'taggable');
    }
~~~

E em cada classe tem que ter esse código abaixo

~~~PHP
    public function tags(){
        return $this->morphToMany(Tag::class,'taggable');
    }
~~~

### Migrations:

tags_table
~~~PHP
public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color');
            $table->timestamps();
        });

        Schema::create('taggables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained('tags');
            $table->morphs('taggable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taggables');
        Schema::dropIfExists('tags');
    }
~~~