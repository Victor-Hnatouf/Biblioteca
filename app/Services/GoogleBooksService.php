<?php

namespace App\Services;

use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class GoogleBooksService
{
    public function isConfigured(): bool
    {
        $key = config('google-books.api_key');

        return is_string($key) && $key !== '';
    }

    public function searchVolumes(string $query, int $startIndex = 0): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('A chave GOOGLE_BOOKS_API_KEY não está configurada no .env.');
        }

        $max = max(1, min(40, (int) config('google-books.max_results', 12)));
        $url = rtrim((string) config('google-books.base_url'), '/').'/volumes';

        $response = Http::timeout((int) config('google-books.timeout', 15))
            ->acceptJson()
            ->get($url, [
                'q' => $query,
                'startIndex' => $startIndex,
                'maxResults' => $max,
                'key' => config('google-books.api_key'),
            ]);

        if (! $response->successful()) {
            Log::warning('Google Books search failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('A pesquisa na Google Books falhou (HTTP '.$response->status().').');
        }

        $data = $response->json();
        $rawItems = is_array($data['items'] ?? null) ? $data['items'] : [];
        $total = (int) ($data['totalItems'] ?? 0);

        $items = [];
        foreach ($rawItems as $volume) {
            if (! is_array($volume)) {
                continue;
            }
            $items[] = $this->summarizeVolume($volume);
        }

        $nextStart = null;
        if ($startIndex + count($items) < $total && count($items) > 0) {
            $nextStart = $startIndex + count($items);
        }

        return [
            'items' => $items,
            'total' => $total,
            'next_start' => $nextStart,
        ];
    }

    public function summarizeVolume(array $volume): array
    {
        $id = (string) ($volume['id'] ?? '');
        $info = is_array($volume['volumeInfo'] ?? null) ? $volume['volumeInfo'] : [];

        $title = (string) ($info['title'] ?? '');
        $subtitle = (string) ($info['subtitle'] ?? '');
        if ($subtitle !== '') {
            $title = trim($title.' — '.$subtitle);
        }

        $authors = is_array($info['authors'] ?? null) ? $info['authors'] : [];
        $authors = array_values(array_filter(array_map('strval', $authors)));

        $publisher = (string) ($info['publisher'] ?? '');
        $description = (string) ($info['description'] ?? '');
        $identifiers = is_array($info['industryIdentifiers'] ?? null) ? $info['industryIdentifiers'] : [];
        $isbn = $this->pickIsbnFromIdentifiers($identifiers);

        $imageLinks = is_array($info['imageLinks'] ?? null) ? $info['imageLinks'] : [];
        $thumb = (string) ($imageLinks['thumbnail'] ?? $imageLinks['smallThumbnail'] ?? '');

        return [
            'volume_id' => $id,
            'title' => $title !== '' ? $title : '(sem título)',
            'authors' => $authors,
            'authors_label' => $authors !== [] ? implode(', ', $authors) : '—',
            'publisher' => $publisher !== '' ? $publisher : null,
            'isbn' => $isbn,
            'description_preview' => Str::limit(strip_tags($description), 280),
            'thumbnail_url' => $thumb !== '' ? $this->upgradeGoogleBooksImageUrl($thumb) : null,
        ];
    }

    public function importVolumeById(string $volumeId): Livro
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('A chave GOOGLE_BOOKS_API_KEY não está configurada no .env.');
        }

        if (Livro::query()->where('google_books_volume_id', $volumeId)->exists()) {
            throw new RuntimeException('Este volume já consta no acervo (importação anterior).');
        }

        $url = rtrim((string) config('google-books.base_url'), '/').'/volumes/'.rawurlencode($volumeId);

        $response = Http::timeout((int) config('google-books.timeout', 15))
            ->acceptJson()
            ->get($url, [
                'key' => config('google-books.api_key'),
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Não foi possível obter o volume na Google Books (HTTP '.$response->status().').');
        }

        $volume = $response->json();
        if (! is_array($volume)) {
            throw new RuntimeException('Resposta inválida da Google Books.');
        }

        $summary = $this->summarizeVolume($volume);
        $info = is_array($volume['volumeInfo'] ?? null) ? $volume['volumeInfo'] : [];
        $description = (string) ($info['description'] ?? '');

        return DB::transaction(function () use ($summary, $description, $volumeId) {
            $editora = $this->findOrCreateEditora($summary['publisher'] ?? null);

            $isbn = $summary['isbn'];
            if ($isbn === null || $isbn === '') {
                $isbn = 'SEM-ISBN-'.Str::upper(Str::limit($volumeId, 16, ''));
            }

            $capaPath = null;
            if (! empty($summary['thumbnail_url'])) {
                $capaPath = $this->downloadCoverToPublicDisk((string) $summary['thumbnail_url'], $volumeId);
            }

            $livro = Livro::create([
                'google_books_volume_id' => $volumeId,
                'isbn' => $isbn,
                'nome' => $summary['title'],
                'editora_id' => $editora->id,
                'bibliografia' => $description !== '' ? $description : null,
                'imagem_capa' => $capaPath,
                'preco' => null,
            ]);

            $autorIds = [];
            foreach ($summary['authors'] as $nomeAutor) {
                $nomeAutor = trim((string) $nomeAutor);
                if ($nomeAutor === '') {
                    continue;
                }
                $autorIds[] = $this->findOrCreateAutor($nomeAutor)->id;
            }
            $livro->autores()->sync($autorIds);

            return $livro->fresh(['editora', 'autores']);
        });
    }

    private function findOrCreateEditora(?string $nome): Editora
    {
        $nome = $nome !== null ? trim($nome) : '';
        if ($nome === '') {
            $nome = 'Editora (Google Books — desconhecida)';
        }

        $existing = Editora::all()->first(fn (Editora $e) => strcasecmp(trim((string) $e->nome), $nome) === 0);
        if ($existing) {
            return $existing;
        }

        return Editora::create(['nome' => $nome]);
    }

    private function findOrCreateAutor(string $nome): Autor
    {
        $existing = Autor::all()->first(fn (Autor $a) => strcasecmp(trim((string) $a->nome), $nome) === 0);
        if ($existing) {
            return $existing;
        }

        return Autor::create(['nome' => $nome]);
    }

    private function pickIsbnFromIdentifiers(array $identifiers): ?string
    {
        $isbn13 = null;
        $isbn10 = null;

        foreach ($identifiers as $row) {
            if (! is_array($row)) {
                continue;
            }
            $type = (string) ($row['type'] ?? '');
            $id = preg_replace('/\s+/', '', (string) ($row['identifier'] ?? ''));
            if ($id === '') {
                continue;
            }
            if ($type === 'ISBN_13') {
                $isbn13 = $id;
            }
            if ($type === 'ISBN_10') {
                $isbn10 = $id;
            }
        }

        if ($isbn13 !== null) {
            return $this->formatIsbn13Display($isbn13);
        }
        if ($isbn10 !== null) {
            return $isbn10;
        }

        return null;
    }

    private function formatIsbn13Display(string $digits): string
    {
        $d = preg_replace('/\D/', '', $digits) ?? '';

        return strlen($d) === 13 ? substr($d, 0, 3).'-'.substr($d, 3, 1).'-'.substr($d, 4, 4).'-'.substr($d, 8, 4).'-'.substr($d, 12, 1) : $digits;
    }

    private function upgradeGoogleBooksImageUrl(string $url): string
    {
        if (str_contains($url, 'zoom=1')) {
            return str_replace('zoom=1', 'zoom=2', $url);
        }

        return $url;
    }

    private function downloadCoverToPublicDisk(string $url, string $volumeId): ?string
    {
        try {
            $image = Http::timeout((int) config('google-books.timeout', 15))
                ->withHeaders(['User-Agent' => 'BibliotecaApp/1.0'])
                ->get($url);

            if (! $image->successful()) {
                return null;
            }

            $ext = 'jpg';
            $ct = strtolower((string) $image->header('Content-Type'));
            if (str_contains($ct, 'png')) {
                $ext = 'png';
            } elseif (str_contains($ct, 'webp')) {
                $ext = 'webp';
            }

            $name = 'capas/google-books-'.Str::slug(Str::limit($volumeId, 40, '')).'-'.Str::random(6).'.'.$ext;
            Storage::disk('public')->put($name, $image->body());

            return $name;
        } catch (\Throwable $e) {
            Log::notice('Falha ao descarregar capa Google Books', [
                'volume' => $volumeId,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
