
# O sistema de roteamento

A variável global $system guarda uma estrutura de objetos em árvore que irá dar acesso a todo o conteúdo disponível.

Para acessar, por exemplo, uma página da administração, podemos chamar assim:

```
$application = $system->child('admin')->child('database');
```

Todos os objetos desta árvore são do tipo eclEngine_application. Noentanto, para se produzirem novos elementos nesta árvore, as classes prefixadas com eclApp serão invocadas estaticamente como ajudantes. Isto nos permite uma enorme flexibilidade, dando a cada elemento da árvore características próprias.

O primeiro passo é indicar, na classe ajudante, quais outras classes poderão ser utilizadas como ajudantes para os objetos que serão filhos do objeto atual.

```
class eclApp_system extends eclApp
{
	public static $map = ['admin', 'system_notFound'];
}
```

Note que o mapa é um array de nomes em strings sem o prefixo 'eclApp_'. O prefixo será adicionado automaticamente, e se a classe não existir, uma classe fantasma será criada e utilizada em seu lugar.

A primeira geração de descendentes de $system corresponde aos diferentes ambientes ou subdomínios. Quando em desenvolvimento em localhost, você pode acessar os diferentes ambientes como subpastas:

```
localhost/admin - para acessar o ambiente administrativo.
localhost/ecolabore - para acessar o domínio padrão.
```

No servidor, você pode configurar um subdomínio coringa (*) e fazê-lo apontar para a pasta raiz do seu domínio principal, deixando que o sistema leia os subdomínios e direcione para o ambiente correspondente:

```
admin.ecolabore.net - acesso ao ambiente administrativo.
www.ecolabore.net - acesso ao ambiente padrão.
ecolabore.net - acesso ao ambiente padrão.
```

Precisamos, portanto, criar a classe eclApp_admin para que o processo possa prosseguir:

```
class eclApp_admin extends eclApp
{
	public static $name = 'admin';
}
```

$name será utilizado pelo roteador para identificar se esta é a classe correta para atender ao domínio indicado pela URL.

Mas para continuar roteando a partir de 'admin', precisamos criar um mapa na classe eclApp_admin:

```
class eclApp_admin extends eclApp
{
	public static $name = 'admin';
	public static $map = ['admin_home', 'adminUsers', 'adminDomains', 'adminSystem', 'adminDatabase', 'adminTools', 'admin_default'];
}
```

Criando as classes correspondentes, o roteador poderá encontrar qual tem o nome correto para atender ao pedido.

```
localhost/admin/database
admin.ecolabore.net/database
```

```
class eclApp_adminDatabase extends eclApp
{
	public static $name = 'database';
	public static $map = ['adminDatabase_config', 'adminDatabase_query', 'adminDatabase_log'];
	public static $control = 'adminDatabase_content';
}
```

Temos aqui também a variável $control, que irá indicar algum conteúdo para a página, tal como o texto do título e do conteúdo.

## Páginas especiais

Embora em nosso exemplo a classe eclApp_admin deva atender à raiz do ambiente administrativo, o roteador irá procurar por algum descendente que responda pelo nome '-home', o que nos leva a ter que criar a classe eclApp_admin_home.

```
class eclApp_admin_home extends eclApp
{
    public static $name = '-home';
    public static $control = 'admin_home';

    public static function constructorHelper(eclEngine_application $me): void
    {
        $me->path = $me->parent->path;
    }
}
```

Embora o roteador tenha acrescentado '-home' ao caminho, o endereço oficial desta página pode ser somente o nome do domínio. Então, podemos copiar o mesmo caminho da aplicação pai para esta aplicação, como mostramos no método constructorHelper() mostrado acima.

Quando o roteador não consegue encontrar nenhuma classe que atenda pelo nome solicitado, ele irá procurar por alguma que atenda a '-default'. Então podemos criar uma aplicação correspondente para 'página não encontrada'. Neste caso, vamos instruir o roteador que ignore o restante da URL, ou então o roteamento continuará falhando:

```
class EclApp_system_notFound extends eclApp
{
    public static $name = '-default';
    public static $control = 'system_notFound';

    static function constructorHelper(eclEngine_application $me): void
    {
        $me->ignoreSubfolders = true;
    }
}
```

# Nomes dinâmicos

Até agora lidamos com nomes estáticos, que são muito úteis para partes da estrutura que são imutáveis, como a área administrativa.

Mas digamos que seja necessário acessar as configurações de um usuário:

```
localhost/admin/users/jose_da_silva
admin.ecolabore.net/users/jose_da_silva
```

Isso dependeria de haver um usuário cadastrado com este identificador. Neste caso, ao invés de termos uma variável com um nome estático, temos de oferecer um método para validar o nome:

```
public static function isChild(eclEngine_application $parent, string $name): bool
{
	global $store;
	return !!$store->user->open($name);
}
```

Isso permitirá que a mesma classe seja reutilizada para todos os usuários.

Quando abrimos a página de usuários, desejamos ver uma lista com todos os usuários cadastrados. Como no nosso exemplo não temos um único nome estático, também precisamos de um método para nos dizer quais são os nomes válidos, se é que eles existem.

```
public static childrenNames(eclEngine_application $parent): array
{
	global $store;
	return $store->user->childrenNames();
}
```

## Despachando a aplicação

Ao terminar o roteamento, teremos um objeto eclEngine_application() que deverá ser despachado. Mais uma vez as classes ajudantes serão invocadas para atuar sobre os objetos.