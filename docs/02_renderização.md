
= Renderização =

A renderização ocorre em três etapas:

* Tokenização, onde o modelo (template) é dividido em pequenos pedaços (tokens).
* Analizador sintático (parser), que irá identificar se a estrutura de tokens corresponde a uma estrutura html válida e 
* renderizador, que irá percorrer a estrutura identificada nos passos anteriores e gerar o código final.

== Tokenizador ==

Para a linguagem HTML o tokenizador é bastante simples, apenas limitando-se a gerar um array onde cada item possui informações do tipo, valor e número da linha.

== Analizador ==

O analizador recebe a lista de tokens e gera uma árvore de "nós", onde cada nó representa uma tag, algum conteúdo estático encontrado entre as tags ou algum conteúdo dinâmico colocado entre as tags.

As tags podem ter atributos sem valor, atributos com valor estático ou atributos com valor dinâmico.

O conteúdo entre a tag de abertura e a tag de fechamento é transformada em nós, filhos da tag atual.

Erros serão lançados se a estrutura do modelo estiver incorreta. As mensagens de erro incluem o nome do modelo e a linha onde o erro ocorreu.

== Renderizador ==

O renderizador irá remontar o código, inserindo os valores dinâmicos e os módulos que forem chamados.

* Atributos especiais como if:true ou if:false podem fazer o renderizador ignorar um trecho de código caso o valor indicado não satisfizer a condição.

* É possível repetir um trecho de código, percorrendo um array indicado pelo atributo for:each. O atributo for:item irá indicar o nome da variável que irá conter o item corrente do array.

* A tag especial <template> não gera código html, mas pode ser útil para percorrer um array com os atributos for:each e for:item, ou saltar algum trecho de código com if:true ou if:false.

* A tag especial <mod> permite inserir um módulo com nome estático ou nome dinâmico. É possível transmitir para o módulo valores estáticos ou dinâmicos, bastando indicá-los como atributos.

* A tag especial <slot> insere, dentro de um módulo, código gerado pelo componente pai entre as tags <mod> e </mod>
