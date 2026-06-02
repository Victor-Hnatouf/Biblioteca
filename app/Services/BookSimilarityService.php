<?php

namespace App\Services;

use App\Models\Livro;
use Illuminate\Support\Collection;

class BookSimilarityService
{
    private array $stopWords = [
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
        'the', 'and', 'of', 'in', 'to', 'is', 'are', 'was', 'were',
        'it', 'that', 'for', 'on', 'with', 'as', 'his', 'her', 'their',
        'an', 'be', 'by', 'at', 'from', 'this', 'they', 'or', 'but',
        'not', 'he', 'she', 'we', 'you', 'have', 'has', 'had',
        'will', 'would', 'could', 'should', 'can', 'may', 'might',
        'its', 'our', 'been', 'also', 'into', 'than', 'then', 'when',
        'there', 'all', 'no', 'up', 'out', 'so', 'if', 'about',
        'who', 'which', 'what', 'how', 'more', 'one', 'two', 'first',
    ];

    public function getRelatedBooks(Livro $livro, int $limit = 4): array
    {
        $allLivros = Livro::disponivelNoCatalogo()
            ->with(['autores', 'editora'])
            ->where('id', '!=', $livro->id)
            ->get();

        if ($allLivros->isEmpty()) {
            return [];
        }

        $livrosComDesc = $allLivros->filter(fn($l) => !empty($l->bibliografia));

        $corpusLivros = collect([$livro])->merge($livrosComDesc);
        $idfScores    = $this->buildIdf($corpusLivros);

        $targetDoc    = $this->getDocument($livro);
        $targetVector = $this->tfidfVector($targetDoc, $idfScores);

        $targetAuthorIds = $livro->autores->pluck('id')->toArray();
        $targetEditoraId = $livro->editora_id;

        $results = [];

        foreach ($allLivros as $other) {
            $textSimilarity = 0.0;
            if (!empty($other->bibliografia)) {
                $otherDoc    = $this->getDocument($other);
                $otherVector = $this->tfidfVector($otherDoc, $idfScores);
                $textSimilarity = $this->cosineSimilarity($targetVector, $otherVector);
            }

            $sharedAuthors = count(array_intersect(
                $targetAuthorIds,
                $other->autores->pluck('id')->toArray()
            ));
            $authorBoost = $sharedAuthors * 0.15;

            $editoraBoost = ($other->editora_id && $other->editora_id === $targetEditoraId) ? 0.05 : 0.0;

            $finalScore = min(1.0, $textSimilarity + $authorBoost + $editoraBoost);

            if ($finalScore > 0.05 || $sharedAuthors > 0) {
                $results[] = [
                    'livro'          => $other,
                    'similarity'     => $finalScore,
                    'shared_authors' => $sharedAuthors,
                ];
            }
        }

        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return array_slice($results, 0, $limit);
    }

    private function getDocument(Livro $livro): array
    {
        $titleText = str_repeat(($livro->nome ?? '') . ' ', 3);
        $bodyText  = $livro->bibliografia ?? '';

        return $this->tokenize($titleText . ' ' . $bodyText);
    }

    private function buildIdf(Collection $livros): array
    {
        $N             = $livros->count();
        $docFrequency  = [];

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

    private function cosineSimilarity(array $vec1, array $vec2): float
    {
        if (empty($vec1) || empty($vec2)) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $magnitude1 = 0.0;
        $magnitude2 = 0.0;

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

    private function tokenize(string $text): array
    {
        $text  = mb_strtolower($text, 'UTF-8');
        $text  = preg_replace('/[^\p{L}\s]/u', ' ', $text);
        $words = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);

        $terms = [];
        foreach ($words as $word) {
            if (mb_strlen($word, 'UTF-8') >= 4 && !in_array($word, $this->stopWords, true)) {
                $terms[$word] = ($terms[$word] ?? 0) + 1;
            }
        }

        return $terms;
    }
}
