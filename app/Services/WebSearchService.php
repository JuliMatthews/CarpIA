<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WebSearchService
{
    public function search(string $query): string
    {
        // DuckDuckGo Instant Answer API (no key needed)
        $response = Http::timeout(10)
            ->get('https://api.duckduckgo.com/', [
                'q' => $query,
                'format' => 'json',
                'no_html' => 1,
                'skip_disambig' => 1,
            ]);

        if (!$response->successful()) {
            return '';
        }

        $data = $response->json();
        $results = [];

        if (!empty($data['AbstractText'])) {
            $results[] = "Resumen: {$data['AbstractText']}";
            if (!empty($data['AbstractSource'])) {
                $results[] = "Fuente: {$data['AbstractSource']}";
            }
            if (!empty($data['AbstractURL'])) {
                $results[] = "URL: {$data['AbstractURL']}";
            }
        }

        if (!empty($data['Answer'])) {
            $results[] = "Respuesta: {$data['Answer']}";
        }

        if (!empty($data['RelatedTopics'])) {
            $results[] = "\nTemas relacionados:";
            foreach (array_slice($data['RelatedTopics'], 0, 3) as $topic) {
                if (isset($topic['Text'])) {
                    $results[] = "- {$topic['Text']}";
                }
            }
        }

        if (!empty($data['Results'])) {
            $results[] = "\nResultados:";
            foreach (array_slice($data['Results'], 0, 3) as $result) {
                if (isset($result['Text'])) {
                    $results[] = "- {$result['Text']}";
                }
            }
        }

        if (empty($results)) {
            return '';
        }

        return "Resultados de búsqueda web para \"{$query}\":\n\n" . implode("\n", $results);
    }
}
