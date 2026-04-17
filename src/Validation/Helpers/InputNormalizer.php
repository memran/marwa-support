<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Helpers;

use Psr\Http\Message\ServerRequestInterface;

class InputNormalizer
{
    /**
     * @param array<string, string|array<int, mixed>> $rules
     * @return array<string, array<int, mixed>>
     */
    public function normalizeRules(array $rules): array
    {
        $normalized = [];

        foreach ($rules as $field => $rule) {
            $normalized[$field] = $this->normalizeFieldRules($rule);
        }

        return $normalized;
    }

    /**
     * @param string|array<int, mixed> $rules
     * @return array<int, mixed>
     */
    public function normalizeFieldRules(string|array $rules): array
    {
        if (is_array($rules)) {
            return array_values($rules);
        }

        return array_filter(array_map('trim', explode('|', $rules)));
    }

    /**
     * @param array<string, mixed> $rules
     * @return array<string, mixed>
     */
    public function normalize(array $rules): array
    {
        return $this->normalizeRules($rules);
    }

    public function extractInput(ServerRequestInterface $request): array
    {
        $input = array_merge(
            $request->getParsedBody() ?? [],
            $this->extractFiles($request)
        );

        return $input;
    }

    private function extractFiles(ServerRequestInterface $request): array
    {
        $files = [];
        foreach ($request->getUploadedFiles() as $key => $uploadedFile) {
            if (is_array($uploadedFile)) {
                $files[$key] = array_map(
                    fn ($file) => $file,
                    $uploadedFile
                );
            } else {
                $files[$key] = $uploadedFile;
            }
        }

        return $files;
    }
}
