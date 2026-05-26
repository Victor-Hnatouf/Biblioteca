<?php

namespace App\Services;

use App\Models\Livro;
use Illuminate\Support\Collection;

/**
 * BookSimilarityService
 *
 * Calcula livros relacionados usando TF-IDF + Cosine Similarity.
 * O título do livro tem peso 3× na análise (repetição para boost de TF).
 * Autores partilhados e mesma editora dão um boost adicional na pontuação final.
 *
 * Nota: como os campos 'nome' e 'bibliografia' são encriptados na base de dados,
 * toda a análise é feita em PHP após desencriptação automática pelo Eloquent.
 */
class BookSimilarityService
{
    /**
     * Stop words em português e inglês (as descrições podem ser bilingues via Google Books).
     */
    private array $stopWords = [
        // Português
        'o', 'a', 'os', 'as', 'um', 'uma', 'uns', 'umas',
        'de', 'do', 'da', 'dos', 'das', 'em', 'no', 'na', 'nos', 'nas',
        'ao', 'aos', 'pelo', 'pela', 'pelos', 'pelas',
        'para', 'por', 'com', 'sem', 'sobre', 'entre', 'sob', 'ante',
        'que', 'quem', 'qual', 'quais', 'como', 'onde', 'quando', 'porque',
        'pois', 'mas', 'nem', 'contudo', 'porém', 'entretanto',
        'ou', 'se', 'não', 'sim', 'também', 'já', 'ainda', 'só',
        'muito', 'pouco', 'mais', 'menos', 'bem', 'mal',
        'este', 'esta', 'estes', 'estas', 'esse', 'essa', 'esses', 'essas',
        'aquele', 'aquela', 'aqueles', 'aquelas', 'isto', 'isso', 'aquilo',
        'todo', 'toda', 'todos', 'todas', 'algum', 'alguma', 'nenhum', 'nenhuma',
        'cada', 'outro', 'outra', 'outros', 'outras', 'mesmo', 'mesma',
        'ele', 'ela', 'eles', 'elas', 'eu', 'tu', 'nós', 'vós',
        'me', 'te', 'se', 'nos', 'vos', 'lhe', 'lhes',
        'foi', 'era', 'ser', 'ter', 'há', 'teve', 'tem', 'são', 'está',
        'num', 'numa', 'nuns', 'numas', 'dum', 'duma',
        'sua', 'seu', 'suas', 'seus',
        // Inglês (frequente em descrições Google Books)
        'the', 'and', 'of', 'in', 'to', 'is', 'are', 'was', 'were',
        'it', 'that', 'for', 'on', 'with', 'as', 'his', 'her', 'their',
        'an', 'be', 'by', 'at', 'from', 'this', 'they', 'or', 'but',
        'not', 'he', 'she', 'we', 'you', 'have', 'has', 'had',
        'will', 'would', 'could', 'should', 'can', 'may', 'might',
        'its', 'our', 'been', 'also', 'into', 'than', 'then', 'when',
        'there', 'all', 'no', 'up', 'out', 'so', 'if', 'about',
        'who', 'which', 'what', 'how', 'more', 'one', 'two', 'first',
    ];

    /**
     * Retorna os livros mais relacionados para um dado livro.
     *
     * @param  Livro  $livro  O livro de referência
     * @param  int    $limit  Máximo de resultados
     * @return array  Array de ['livro' => Livro, 'similarity' => float, 'shared_authors' => int]
     */
    public function getRelatedBooks(Livro $livro, int $limit = 4): array
    {
        // Carregar todos os outros livros com relações necessárias
        $allLivros = Livro::with(['autores', 'editora'])
            ->where('id', '!=', $livro->id)
            ->get();

        if ($allLivros->isEmpty()) {
            return [];
        }

        // Separar livros com descrição (usados para TF-IDF) dos sem descrição
        $livrosComDesc = $allLivros->filter(fn($l) => !empty($l->bibliografia));

        // Construir corpus para cálculo de IDF (todos os livros com descrição + livro atual)
        $corpusLivros = collect([$livro])->merge($livrosComDesc);
        $idfScores    = $this->buildIdf($corpusLivros);

        // Vetor TF-IDF do livro alvo
        $targetDoc    = $this->getDocument($livro);
        $targetVector = $this->tfidfVector($targetDoc, $idfScores);

        // Dados do livro alvo para boosting
        $targetAuthorIds = $livro->autores->pluck('id')->toArray();
        $targetEditoraId = $livro->editora_id;

        $results = [];

        foreach ($allLivros as $other) {
            // Similaridade textual (0 se não tiver descrição)
            $textSimilarity = 0.0;
            if (!empty($other->bibliografia)) {
                $otherDoc    = $this->getDocument($other);
                $otherVector = $this->tfidfVector($otherDoc, $idfScores);
                $textSimilarity = $this->cosineSimilarity($targetVector, $otherVector);
            }

            // Boost por autores partilhados (+15% por autor)
            $sharedAuthors = count(array_intersect(
                $targetAuthorIds,
                $other->autores->pluck('id')->toArray()
            ));
            $authorBoost = $sharedAuthors * 0.15;

            // Boost pequeno por mesma editora (+5%)
            $editoraBoost = ($other->editora_id && $other->editora_id === $targetEditoraId) ? 0.05 : 0.0;

            $finalScore = min(1.0, $textSimilarity + $authorBoost + $editoraBoost);

            // Incluir apenas se tiver alguma relevância
            if ($finalScore > 0.05 || $sharedAuthors > 0) {
                $results[] = [
                    'livro'          => $other,
                    'similarity'     => $finalScore,
                    'shared_authors' => $sharedAuthors,
                ];
            }
        }

        // Ordenar por pontuação decrescente e limitar
        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return array_slice($results, 0, $limit);
    }

    // -------------------------------------------------------------------------
    // Métodos internos
    // -------------------------------------------------------------------------

    /**
     * Gera o documento de texto de um livro.
     * O título é repetido 3× para dar mais peso ao tema principal.
     */
    private function getDocument(Livro $livro): array
    {
        $titleText = str_repeat(($livro->nome ?? '') . ' ', 3);
        $bodyText  = $livro->bibliografia ?? '';

        return $this->tokenize($titleText . ' ' . $bodyText);
    }

    /**
     * Constrói os IDF scores para todos os termos do corpus.
     * Usa IDF suavizado: idf(t) = log((N + 1) / (df(t) + 1)) + 1
     */
    private function buildIdf(Collection $livros): array
    {
        $N             = $livros->count();
        $docFrequency  = []; // quantos documentos contêm cada termo

        foreach ($livros as $livro) {
            $text  = ($livro->nome ?? '') . ' ' . ($livro->bibliografia ?? '');
            $terms = array_keys($this->tokenize($text));

            foreach ($terms as $term) {
                $docFrequency[$term] = ($docFrequency[$term] ?? 0) + 1;
            }
        }

        $idf = [];
        foreach ($docFrequency as $term => $df) {
            $idf[$term] = log(($N + 1) / ($df + 1)) + 1;
        }

        return $idf;
    }

    /**
     * Calcula o vetor TF-IDF de um documento.
     * TF(t, d) = count(t in d) / total_terms(d)
     */
    private function tfidfVector(array $termFrequencies, array $idfScores): array
    {
        $totalTerms = array_sum($termFrequencies);

        if ($totalTerms === 0) {
            return [];
        }

        $vector = [];
        foreach ($termFrequencies as $term => $count) {
            $tf           = $count / $totalTerms;
            $idf          = $idfScores[$term] ?? 1.0;
            $vector[$term] = $tf * $idf;
        }

        return $vector;
    }

    /**
     * Cosine Similarity entre dois vetores TF-IDF.
     * cos(θ) = (A · B) / (|A| × |B|)
     */
    private function cosineSimilarity(array $vec1, array $vec2): float
    {
        if (empty($vec1) || empty($vec2)) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $magnitude1 = 0.0;
        $magnitude2 = 0.0;

        // Só itera nos termos do vec1 para o produto escalar (otimização)
        foreach ($vec1 as $term => $val1) {
            $val2        = $vec2[$term] ?? 0.0;
            $dotProduct += $val1 * $val2;
            $magnitude1 += $val1 * $val1;
        }

        foreach ($vec2 as $val2) {
            $magnitude2 += $val2 * $val2;
        }

        $denom = sqrt($magnitude1) * sqrt($magnitude2);

        return $denom > 0.0 ? $dotProduct / $denom : 0.0;
    }

    /**
     * Tokeniza texto em array de termo => frequência.
     * Remove pontuação, converte para minúsculas e filtra stop words e palavras curtas.
     */
    private function tokenize(string $text): array
    {
        // Normalizar: minúsculas + remover pontuação/números
        $text  = mb_strtolower($text, 'UTF-8');
        $text  = preg_replace('/[^\p{L}\s]/u', ' ', $text);
        $words = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);

        $terms = [];
        foreach ($words as $word) {
            // Mínimo 4 letras e não é stop word
            if (mb_strlen($word, 'UTF-8') >= 4 && !in_array($word, $this->stopWords, true)) {
                $terms[$word] = ($terms[$word] ?? 0) + 1;
            }
        }

        return $terms;
    }
}
