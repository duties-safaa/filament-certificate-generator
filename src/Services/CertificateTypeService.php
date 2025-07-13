<?php

namespace HusamTariq\FilamentCertificateGenerator\Services;

use HusamTariq\FilamentCertificateGenerator\Concerns\HasCertificateTypes;

class CertificateTypeService implements HasCertificateTypes
{
    public function getTypeDisplay(string $typeKey, bool $forSelect = false): string|array
    {
        $types = config('certificate-generator.types', []);

        $translatedLabel = match($typeKey) {
            'qualification' => __('filament-certificate-generator::certificate-generator.resource.certificate-qualification'),
            'participation' => __('filament-certificate-generator::certificate-generator.resource.certificate-participation'),
            default => $types[$typeKey]['label'] ?? $typeKey
        };

        return $forSelect ? [$typeKey => $translatedLabel] : $translatedLabel;
    }

    public function getTypeOptions(): array
    {
        return collect($this->getCertificateTypes())
            ->mapWithKeys(fn ($config, $key) => $this->getTypeDisplay($key, true))
            ->toArray();
    }

    public function getTypeColor(string $typeKey): string
    {
        return config("certificate-generator.types.$typeKey.color", 'gray');
    }

    public function getTypeIcon(string $typeKey): ?string
    {
        return config("certificate-generator.types.$typeKey.icon");
    }

    public function getCertificateTypes(): array
    {
        return config('certificate-generator.types', []);
    }
}
