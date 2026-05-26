<?php

namespace Tests\Unit;

use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use App\Services\BookSimilarityService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookSimilarityServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookSimilarityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BookSimilarityService();
    }

    /** @test */
    public function test_it_calculates_similarity_based_on_descriptions()
    {
        // Criar editora e autores
        $editora = Editora::create([
            'nome' => 'Editora Teste',
            'logotipo' => 'logo.png',
        ]);
        $autor = Autor::create([
            'nome' => 'Autor Teste',
            'foto' => 'foto.png',
        ]);

        // Livro Alvo (sobre PHP e Programação)
        $target = Livro::create([
            'nome' => 'Programação Web Moderno com PHP',
            'isbn' => '9781234567890',
            'editora_id' => $editora->id,
            'bibliografia' => 'Um guia completo sobre programação de sistemas web modernos utilizando a linguagem PHP Laravel, cobrindo tópicos avançados de desenvolvimento de software e arquitetura.'
        ]);
        $target->autores()->attach($autor);

        // Livro Muito Similar (PHP e Laravel)
        $similar = Livro::create([
            'nome' => 'Desenvolvimento Web Eficiente Laravel PHP',
            'isbn' => '9781234567891',
            'editora_id' => $editora->id,
            'bibliografia' => 'Aprenda a criar aplicações web modernas com Laravel e PHP. Focado em desenvolvimento ágil de software usando boas práticas de programação e arquitetura de sistemas.'
        ]);
        $similar->autores()->attach($autor);

        // Livro Diferente (Culinária)
        $different = Livro::create([
            'nome' => 'Segredos da Culinária Tradicional',
            'isbn' => '9781234567892',
            'editora_id' => $editora->id,
            'bibliografia' => 'Receitas maravilhosas de bolos, massas e pratos principais da cozinha tradicional. Dicas de culinária para preparar refeições deliciosas na sua cozinha.'
        ]);

        $related = $this->service->getRelatedBooks($target);

        $this->assertNotEmpty($related);
        
        // O livro similar deve estar em primeiro lugar com pontuação superior ao livro diferente
        $firstRelated = $related[0];
        $this->assertEquals($similar->id, $firstRelated['livro']->id);
        
        // A similaridade deve ser significativamente maior
        if (isset($related[1])) {
            $this->assertTrue($related[0]['similarity'] > $related[1]['similarity']);
        }
    }
}
